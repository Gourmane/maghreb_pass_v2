import { lazy, Suspense, useEffect, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { DetailView, FavoritesView, PublicCatalog } from './components/catalog.jsx';
import { HomePage } from './components/home.jsx';
import { AppFooter, AppHeader } from './components/layout.jsx';
import { api, setAuthToken, unwrapPage } from './lib/api.js';
import { buildParams, initialForms, modules, routeFromPath } from './lib/catalog.js';

const AdminView = lazy(() => import('./components/admin.jsx').then((module) => ({ default: module.AdminView })));
const MapView = lazy(() => import('./components/map.jsx').then((module) => ({ default: module.MapView })));
const ProfileView = lazy(() => import('./components/profile.jsx').then((module) => ({ default: module.ProfileView })));
const AdminReservationsView = lazy(() => import('./components/reservations.jsx').then((module) => ({ default: module.AdminReservationsView })));
const MyReservationsView = lazy(() => import('./components/reservations.jsx').then((module) => ({ default: module.MyReservationsView })));
const TripPlannerView = lazy(() => import('./components/trip-planner.jsx').then((module) => ({ default: module.TripPlannerView })));

function readSession() {
  const raw = localStorage.getItem('maghrebpass_user');
  if (!raw) return { token: '', user: null };

  try {
    return { token: '', user: JSON.parse(raw) };
  } catch {
    return { token: '', user: null };
  }
}

function App() {
  const { t, i18n } = useTranslation();
  const initialRoute = routeFromPath();
  const [route, setRoute] = useState(initialRoute);
  const [activeModule, setActiveModule] = useState(initialRoute.module || 'matches');
  const [session, setSession] = useState(readSession);
  const [catalog, setCatalog] = useState({ matches: [], hotels: [], restaurants: [], attractions: [], packages: [] });
  const [detailItem, setDetailItem] = useState(null);
  const [matchNearby, setMatchNearby] = useState(null);
  const [favorites, setFavorites] = useState({ hotels: [], restaurants: [], attractions: [] });
  const [stats, setStats] = useState(null);
  const [users, setUsers] = useState([]);
  const [filters, setFilters] = useState({ city: '', search: '', group_name: '', phase: '', date: '', stars: '', price_range: '', category: '' });
  const [authMode, setAuthMode] = useState(initialRoute.authMode || 'login');
  const [authForm, setAuthForm] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    preferred_language: i18n.language,
  });
  const [profileForm, setProfileForm] = useState({ name: '', preferred_language: i18n.language, avatar_url: '' });
  const [adminForm, setAdminForm] = useState(initialForms[activeModule]);
  const [editingId, setEditingId] = useState(null);
  const [loading, setLoading] = useState(false);
  const [notice, setNotice] = useState('');
  const [error, setError] = useState('');

  const currentModule = modules.find((module) => module.key === activeModule);
  const isAdmin = session.user?.role === 'admin';

  useEffect(() => {
    const onPop = () => {
      const next = routeFromPath();
      setRoute(next);
      if (next.module) setActiveModule(next.module);
      if (next.authMode) setAuthMode(next.authMode);
    };
    window.addEventListener('popstate', onPop);
    return () => window.removeEventListener('popstate', onPop);
  }, []);

  useEffect(() => {
    setAuthToken(session.token);
    if (session.user) localStorage.setItem('maghrebpass_user', JSON.stringify(session.user));
    else localStorage.removeItem('maghrebpass_user');
    setProfileForm({
      name: session.user?.name || '',
      preferred_language: session.user?.preferred_language || i18n.language,
      avatar_url: session.user?.avatar_url || '',
    });
  }, [session, i18n.language]);

  useEffect(() => {
    hydrateSession();
  }, []);

  useEffect(() => {
    if (route.view === 'home') loadHomeCatalog();
    else loadModule(activeModule);
    setAdminForm(initialForms[activeModule]);
    setEditingId(null);
  }, [activeModule, route.view]);

  useEffect(() => {
    if (route.view === 'detail' && route.id) loadDetail(route.module, route.id);
  }, [route.view, route.module, route.id]);

  useEffect(() => {
    if (session.user) loadFavorites();
    if (isAdmin) loadAdmin();
  }, [session.user?.id, session.user?.role]);

  useEffect(() => {
    document.documentElement.lang = i18n.language;
    document.documentElement.dir = i18n.dir(i18n.language);
    localStorage.setItem('maghrebpass_language', i18n.language);
  }, [i18n, i18n.language]);

  const cities = useMemo(() => {
    const values = Object.values(catalog).flat().map((item) => item.city).filter(Boolean);
    return [...new Set(values)].sort();
  }, [catalog]);

  function navigate(path, next = routeFromPath(path)) {
    window.history.pushState({}, '', path);
    setRoute(next);
    if (next.module) setActiveModule(next.module);
    if (next.authMode) setAuthMode(next.authMode);
  }

  async function request(action, successMessage = '') {
    setLoading(true);
    setError('');
    setNotice('');

    try {
      const result = await action();
      if (successMessage) setNotice(successMessage);
      return result;
    } catch (err) {
      setError(err.response?.data?.message || t('messages.apiOffline'));
      return null;
    } finally {
      setLoading(false);
    }
  }

  async function hydrateSession() {
    try {
      const response = await api.get('/auth/me');
      setSession({ token: '', user: response.data.user });
      i18n.changeLanguage(response.data.user.preferred_language || i18n.language);
    } catch (err) {
      if (![401, 419].includes(err.response?.status)) setError(err.response?.data?.message || t('messages.apiOffline'));
    }
  }

  async function loadHomeCatalog() {
    await request(async () => {
      const responses = await Promise.all(modules.map((module) => api.get(`/${module.key}`)));
      setCatalog((current) => modules.reduce((next, module, index) => ({
        ...next,
        [module.key]: unwrapPage(responses[index]),
      }), current));
    });
  }

  async function loadModule(moduleKey = activeModule) {
    const params = buildParams(moduleKey, filters);
    await request(async () => {
      const response = await api.get(`/${moduleKey}`, { params });
      setCatalog((current) => ({ ...current, [moduleKey]: unwrapPage(response) }));
    });
  }

  async function loadDetail(moduleKey, id) {
    setDetailItem(null);
    setMatchNearby(null);
    await request(async () => {
      const response = await api.get(`/${moduleKey}/${id}`);
      setDetailItem(response.data.data);
      if (moduleKey === 'matches') {
        const nearbyResponse = await api.get(`/matches/${id}/nearby`);
        setMatchNearby(nearbyResponse.data);
      }
    });
  }

  async function loadFavorites() {
    await request(async () => {
      const response = await api.get('/favorites');
      setFavorites(response.data.data);
    });
  }

  async function loadAdmin() {
    await request(async () => {
      const [statsResponse, usersResponse] = await Promise.all([
        api.get('/admin/stats'),
        api.get('/admin/users'),
      ]);
      setStats(statsResponse.data);
      setUsers(unwrapPage(usersResponse));

      const responses = await Promise.all(modules.map((module) => api.get(`/admin/${module.key}`).catch(() => api.get(`/${module.key}`))));
      setCatalog((current) => modules.reduce((next, module, index) => ({
        ...next,
        [module.key]: unwrapPage(responses[index]),
      }), current));
    });
  }

  async function submitAuth(event) {
    event.preventDefault();
    const endpoint = authMode === 'login' ? '/auth/login' : '/auth/register';
    const payload = authMode === 'login'
      ? { email: authForm.email, password: authForm.password }
      : authForm;

    await request(async () => {
      const response = await api.post(endpoint, payload);
      setSession({ token: '', user: response.data.user });
      i18n.changeLanguage(response.data.user.preferred_language || i18n.language);
      if (route.view === 'admin') {
        if (response.data.user.role === 'admin') navigate('/admin');
        else {
          setError(t('admin.lockedBody'));
          navigate('/profile');
        }
        return;
      }
      navigate('/');
    }, t('messages.saved'));
  }

  async function forgotPassword() {
    await request(async () => {
      await api.post('/auth/forgot-password', { email: authForm.email });
    }, t('messages.resetSent'));
  }

  async function submitProfile(event) {
    event.preventDefault();
    await request(async () => {
      const response = await api.put('/auth/profile', profileForm);
      setSession({ token: '', user: response.data.user });
      i18n.changeLanguage(response.data.user.preferred_language);
    }, t('messages.saved'));
  }

  async function logout() {
    await api.post('/auth/logout').catch(() => {});
    setSession({ token: '', user: null });
    setFavorites({ hotels: [], restaurants: [], attractions: [] });
    navigate('/');
  }

  async function addFavorite(item, favoriteType = currentModule.favoriteType) {
    if (!session.user) {
      setError(t('messages.requiredAuth'));
      navigate('/login');
      return;
    }

    if (!favoriteType) return;

    await request(async () => {
      await api.post('/favorites', { type: favoriteType, id: item.id });
      await loadFavorites();
    }, t('messages.saved'));
  }

  async function removeFavorite(id) {
    await request(async () => {
      await api.delete(`/favorites/${id}`);
      await loadFavorites();
    }, t('messages.saved'));
  }

  async function submitAdmin(event) {
    event.preventDefault();
    const payload = { ...adminForm };
    if (Array.isArray(payload.photos)) payload.photos = payload.photos.filter(Boolean);

    await request(async () => {
      if (editingId) await api.put(`/admin/${activeModule}/${editingId}`, payload);
      else await api.post(`/admin/${activeModule}`, payload);

      setAdminForm(initialForms[activeModule]);
      setEditingId(null);
      await loadModule(activeModule);
      await loadAdmin();
    }, t('messages.saved'));
  }

  async function deleteAdminItem(item) {
    await request(async () => {
      await api.delete(`/admin/${activeModule}/${item.id}`);
      await loadModule(activeModule);
      await loadAdmin();
    }, t('messages.saved'));
  }

  function editAdminItem(item) {
    const form = { ...initialForms[activeModule] };
    Object.keys(form).forEach((key) => {
      if (item[key] !== undefined && item[key] !== null) form[key] = item[key];
    });
    setAdminForm(form);
    setEditingId(item.id);
  }

  function changeLanguage(language) {
    i18n.changeLanguage(language);
    localStorage.setItem('maghrebpass_language', language);
  }

  const publicActive = ['public', 'detail'].includes(route.view);

  return (
    <div className="app-shell">
      <AppHeader
        activeModule={activeModule}
        changeLanguage={changeLanguage}
        i18n={i18n}
        navigate={navigate}
        onLogout={logout}
        publicActive={publicActive}
        route={route}
        session={session}
        modules={modules}
        t={t}
      />

      <main className="page-main">
        {(notice || error) && <div aria-live="polite" className={`notice ${error ? 'error' : ''}`} role={error ? 'alert' : 'status'}>{error || notice}</div>}

        {route.view === 'home' && (
          <HomePage
            catalog={catalog}
            language={i18n.language}
            loading={loading}
            navigate={navigate}
            onAddFavorite={addFavorite}
            session={session}
            t={t}
          />
        )}

        {route.view === 'public' && (
          <PublicCatalog
            activeModule={activeModule}
            catalog={catalog}
            cities={cities}
            currentModule={currentModule}
            filters={filters}
            language={i18n.language}
            loading={loading}
            modules={modules}
            onAddFavorite={addFavorite}
            onFilterChange={setFilters}
            onLoad={() => loadModule(activeModule)}
            onModuleChange={(key) => navigate(modules.find((module) => module.key === key).basePath)}
            onOpenDetail={(item) => navigate(`${currentModule.basePath}/${item.id}`)}
            t={t}
          />
        )}

        {route.view === 'detail' && (
          <DetailView
            item={detailItem}
            language={i18n.language}
            loading={loading}
            matchNearby={matchNearby}
            moduleKey={activeModule}
            onAddFavorite={addFavorite}
            onBack={() => navigate(currentModule.basePath)}
            onOpenNearby={(moduleKey, item) => navigate(`/${moduleKey}/${item.id}`)}
            session={session}
            t={t}
          />
        )}

        {route.view === 'favorites' && (
          <FavoritesView favorites={favorites} language={i18n.language} onRemove={removeFavorite} t={t} />
        )}

        {route.view === 'map' && (
          <Suspense fallback={<section className="content-panel"><span>{t('messages.loading')}</span></section>}>
            <MapView navigate={navigate} t={t} />
          </Suspense>
        )}

        {route.view === 'trips' && (
          <Suspense fallback={<section className="content-panel"><span>{t('messages.loading')}</span></section>}>
            <TripPlannerView language={i18n.language} loading={loading} navigate={navigate} session={session} t={t} />
          </Suspense>
        )}

        {route.view === 'my-reservations' && (
          <Suspense fallback={<section className="content-panel"><span>{t('messages.loading')}</span></section>}>
            {!session.user ? <ProfileView authForm={authForm} authMode="login" loading={loading} onAuthFormChange={setAuthForm} onAuthModeChange={() => navigate('/login')} onForgotPassword={forgotPassword} onProfileChange={setProfileForm} onProfileSubmit={submitProfile} onSubmit={submitAuth} profileForm={profileForm} session={session} t={t} /> : <MyReservationsView t={t} />}
          </Suspense>
        )}

        {route.view === 'profile' && (
          <Suspense fallback={<section className="content-panel"><span>{t('messages.loading')}</span></section>}>
            <ProfileView
              authForm={authForm}
              authMode={authMode}
              loading={loading}
              onAuthFormChange={setAuthForm}
              onAuthModeChange={(mode) => navigate(mode === 'login' ? '/login' : '/register')}
              onForgotPassword={forgotPassword}
              onProfileChange={setProfileForm}
              onProfileSubmit={submitProfile}
              onSubmit={submitAuth}
              profileForm={profileForm}
              session={session}
              t={t}
            />
          </Suspense>
        )}

        {route.view === 'admin' && (
          <Suspense fallback={<section className="content-panel"><span>{t('messages.loading')}</span></section>}>
            {!session.user ? (
              <ProfileView
                allowRegister={false}
                authForm={authForm}
                authMode="login"
                loading={loading}
                onAuthFormChange={setAuthForm}
                onAuthModeChange={() => navigate('/login')}
                onForgotPassword={forgotPassword}
                onProfileChange={setProfileForm}
                onProfileSubmit={submitProfile}
                onSubmit={submitAuth}
                profileForm={profileForm}
                session={session}
                t={t}
              />
            ) : (
              <AdminView
                activeModule={activeModule}
                adminForm={adminForm}
                catalog={catalog}
                editingId={editingId}
                isAdmin={isAdmin}
                loading={loading}
                modules={modules}
                navigate={navigate}
                onDelete={deleteAdminItem}
                onEdit={editAdminItem}
                onFormChange={setAdminForm}
                onModuleChange={(key) => navigate(key === 'matches' ? '/admin/matches' : `/admin/${key}`)}
                onReset={() => { setAdminForm(initialForms[activeModule]); setEditingId(null); }}
                onSubmit={submitAdmin}
                stats={stats}
                t={t}
                users={users}
              />
            )}
          </Suspense>
        )}

        {route.view === 'admin-reservations' && (
          <Suspense fallback={<section className="content-panel"><span>{t('messages.loading')}</span></section>}>
            {!session.user ? (
              <ProfileView allowRegister={false} authForm={authForm} authMode="login" loading={loading} onAuthFormChange={setAuthForm} onAuthModeChange={() => navigate('/login')} onForgotPassword={forgotPassword} onProfileChange={setProfileForm} onProfileSubmit={submitProfile} profileForm={profileForm} session={session} t={t} onSubmit={submitAuth} />
            ) : isAdmin ? (
              <AdminReservationsView t={t} />
            ) : (
              <section className="content-panel locked"><h2>{t('admin.lockedTitle')}</h2><p>{t('admin.lockedBody')}</p></section>
            )}
          </Suspense>
        )}

      </main>

      <AppFooter changeLanguage={changeLanguage} i18n={i18n} navigate={navigate} session={session} t={t} />
    </div>
  );
}

export default App;
