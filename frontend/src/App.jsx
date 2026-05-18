import {
  CalendarDays,
  Check,
  ExternalLink,
  Heart,
  Hotel,
  Languages,
  LogIn,
  LogOut,
  MapPin,
  Phone,
  Plus,
  Shield,
  Sparkles,
  Trash2,
  Trophy,
  Utensils,
} from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { api, setAuthToken, unwrapPage } from './lib/api.js';

const modules = [
  { key: 'matches', icon: Trophy, favoriteType: null, basePath: '/matches' },
  { key: 'hotels', icon: Hotel, favoriteType: 'hotel', basePath: '/hotels' },
  { key: 'restaurants', icon: Utensils, favoriteType: 'restaurant', basePath: '/restaurants' },
  { key: 'attractions', icon: MapPin, favoriteType: 'attraction', basePath: '/attractions' },
];

const initialForms = {
  matches: {
    team_home: 'Maroc',
    team_home_code: 'MAR',
    team_home_flag_url: 'https://flagcdn.com/w320/ma.png',
    team_away: 'Portugal',
    team_away_code: 'POR',
    team_away_flag_url: 'https://flagcdn.com/w320/pt.png',
    match_date: '2030-06-14',
    match_time: '20:00',
    stadium: 'Grand Stade Hassan II',
    city: 'Casablanca / Benslimane',
    group_name: 'Groupe A',
    phase: 'group',
    status: 'upcoming',
  },
  hotels: {
    name: 'Hotel Demo Front',
    description_fr: 'Hotel confortable proche des sites principaux.',
    description_en: 'Comfortable hotel near the main sites.',
    city: 'Casablanca',
    district: 'Centre',
    stars: 4,
    price_min: 800,
    price_max: 1400,
    currency: 'MAD',
    website_url: 'https://example.test',
    phone: '+212 500 000 000',
    email: 'hotel@example.test',
    photos: [
      'https://upload.wikimedia.org/wikipedia/commons/6/6f/Hassan_II_Mosque_Casablanca_Morocco.jpg',
      'https://upload.wikimedia.org/wikipedia/commons/0/06/The_Open_Area_of_Hassan_II_Mosque_-_Casablanca_Morocco.jpg',
    ],
  },
  restaurants: {
    name: 'Restaurant Demo Front',
    description_fr: 'Cuisine marocaine et internationale.',
    description_en: 'Moroccan and international cuisine.',
    city: 'Rabat',
    address: 'Centre',
    cuisine_type: 'Marocaine',
    price_range: 'moyen',
    phone: '+212 500 000 000',
    whatsapp: '+212 600 000 000',
    photos: [
      'https://upload.wikimedia.org/wikipedia/commons/5/5a/Moroccan_tajine_with_meat.jpg',
      'https://upload.wikimedia.org/wikipedia/commons/4/4d/Tajines_in_a_pottery_shop_in_Morocco.jpg',
    ],
  },
  attractions: {
    name: 'Attraction Demo Front',
    description_fr: 'Site culturel de demonstration.',
    description_en: 'Demo cultural site.',
    city: 'Fes',
    address: 'Medina',
    category: 'Musee',
    entry_price: 40,
    opening_hours: '09:00-18:00',
    photos: ['https://upload.wikimedia.org/wikipedia/commons/2/25/Marrakech_Majorelle_Garden_317.JPG'],
  },
};

function routeFromPath(pathname = window.location.pathname) {
  const clean = pathname.replace(/\/+$/, '') || '/';
  const adminMatch = clean.match(/^\/admin(?:\/(matches|hotels|restaurants|attractions|users))?$/);
  if (adminMatch) return { view: 'admin', module: adminMatch[1] || 'matches' };
  if (clean === '/' || clean === '/matches') return { view: 'public', module: 'matches' };
  if (clean === '/login') return { view: 'profile', authMode: 'login' };
  if (clean === '/register') return { view: 'profile', authMode: 'register' };
  if (clean === '/profile') return { view: 'profile' };
  if (clean === '/favorites') return { view: 'favorites' };

  for (const module of modules) {
    if (clean === module.basePath) return { view: 'public', module: module.key };
    const detail = clean.match(new RegExp(`^${module.basePath}/(\\d+)$`));
    if (detail) return { view: 'detail', module: module.key, id: Number(detail[1]) };
  }

  return { view: 'public', module: 'matches' };
}

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
  const [catalog, setCatalog] = useState({ matches: [], hotels: [], restaurants: [], attractions: [] });
  const [detailItem, setDetailItem] = useState(null);
  const [favorites, setFavorites] = useState({ hotels: [], restaurants: [], attractions: [] });
  const [stats, setStats] = useState(null);
  const [users, setUsers] = useState([]);
  const [filters, setFilters] = useState({ city: '', search: '', group_name: '', phase: '', date: '', stars: '', price_range: '', category: '' });
  const [authMode, setAuthMode] = useState(initialRoute.authMode || 'login');
  const [authForm, setAuthForm] = useState({
    name: '',
    email: 'tourist@maghrebpass.test',
    password: 'password',
    password_confirmation: 'password',
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
    loadModule(activeModule);
  }, []);

  useEffect(() => {
    loadModule(activeModule);
    setAdminForm(initialForms[activeModule]);
    setEditingId(null);
  }, [activeModule]);

  useEffect(() => {
    if (route.view === 'detail' && route.id) loadDetail(route.module, route.id);
  }, [route.view, route.module, route.id]);

  useEffect(() => {
    if (session.user) loadFavorites();
    if (isAdmin) loadAdmin();
  }, [session.user?.id, session.user?.role]);

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
      console.error('MaghrebPass API request failed', err);
      setError(err.response?.data?.message || t('messages.apiOffline'));
      return null;
    } finally {
      setLoading(false);
    }
  }

  async function hydrateSession() {
    await request(async () => {
      const response = await api.get('/auth/me');
      setSession({ token: '', user: response.data.user });
      i18n.changeLanguage(response.data.user.preferred_language || i18n.language);
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
    await request(async () => {
      const response = await api.get(`/${moduleKey}/${id}`);
      setDetailItem(response.data.data);
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

  async function demoLogin(role) {
    const email = role === 'admin' ? 'admin@maghrebpass.test' : 'tourist@maghrebpass.test';
    await request(async () => {
      const response = await api.post('/auth/login', { email, password: 'password' });
      setSession({ token: '', user: response.data.user });
      i18n.changeLanguage(response.data.user.preferred_language || 'fr');
      navigate(role === 'admin' ? '/admin' : '/');
    }, t('messages.saved'));
  }

  async function logout() {
    await api.post('/auth/logout').catch(() => {});
    setSession({ token: '', user: null });
    setFavorites({ hotels: [], restaurants: [], attractions: [] });
    navigate('/');
  }

  async function addFavorite(item) {
    if (!session.user) {
      setError(t('messages.requiredAuth'));
      navigate('/login');
      return;
    }

    await request(async () => {
      await api.post('/favorites', { type: currentModule.favoriteType, id: item.id });
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
      <header className="topbar">
        <button className="brand" onClick={() => navigate('/')} type="button">
          <Sparkles size={22} />
          <span>{t('appName')}</span>
        </button>

        <nav className="nav-tabs" aria-label="Main navigation">
          <button className={publicActive ? 'active' : ''} onClick={() => navigate(`/${activeModule === 'matches' ? 'matches' : activeModule}`)} type="button">{t('nav.public')}</button>
          <button className={route.view === 'favorites' ? 'active' : ''} onClick={() => navigate('/favorites')} type="button">{t('nav.favorites')}</button>
          <button className={route.view === 'admin' ? 'active' : ''} onClick={() => navigate('/admin')} type="button">{t('nav.admin')}</button>
          <button className={route.view === 'profile' ? 'active' : ''} onClick={() => navigate(session.user ? '/profile' : '/login')} type="button">{t('nav.profile')}</button>
        </nav>

        <div className="header-actions">
          <button className="icon-button" onClick={() => changeLanguage(i18n.language === 'fr' ? 'en' : 'fr')} title="Language" type="button">
            <Languages size={18} />
            <span>{i18n.language.toUpperCase()}</span>
          </button>
          {session.user ? (
            <button className="ghost-button" onClick={logout} type="button"><LogOut size={17} />{t('auth.logout')}</button>
          ) : (
            <button className="ghost-button" onClick={() => navigate('/login')} type="button"><LogIn size={17} />{t('auth.login')}</button>
          )}
        </div>
      </header>

      <main>
        <section className="hero-band">
          <div>
            <p className="eyebrow">World Cup Morocco 2030</p>
            <h1>{t('tagline')}</h1>
          </div>
          <div className="hero-status">
            <span>{session.user ? session.user.name : 'Guest'}</span>
            <strong>{session.user?.role || 'visitor'}</strong>
          </div>
        </section>

        {(notice || error) && <div className={`notice ${error ? 'error' : ''}`}>{error || notice}</div>}

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
            moduleKey={activeModule}
            onAddFavorite={addFavorite}
            onBack={() => navigate(currentModule.basePath)}
            t={t}
          />
        )}

        {route.view === 'favorites' && (
          <FavoritesView favorites={favorites} language={i18n.language} onRemove={removeFavorite} t={t} />
        )}

        {route.view === 'profile' && (
          <ProfileView
            authForm={authForm}
            authMode={authMode}
            onAuthFormChange={setAuthForm}
            onAuthModeChange={(mode) => navigate(mode === 'login' ? '/login' : '/register')}
            onDemoLogin={demoLogin}
            onForgotPassword={forgotPassword}
            onProfileChange={setProfileForm}
            onProfileSubmit={submitProfile}
            onSubmit={submitAuth}
            profileForm={profileForm}
            session={session}
            t={t}
          />
        )}

        {route.view === 'admin' && (
          <AdminView
            activeModule={activeModule}
            adminForm={adminForm}
            catalog={catalog}
            editingId={editingId}
            isAdmin={isAdmin}
            modules={modules}
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
      </main>
    </div>
  );
}

function PublicCatalog({ activeModule, catalog, cities, currentModule, filters, language, loading, modules, onAddFavorite, onFilterChange, onLoad, onModuleChange, onOpenDetail, t }) {
  const items = catalog[activeModule] || [];

  return (
    <section className="workspace">
      <ModuleRail activeModule={activeModule} modules={modules} onModuleChange={onModuleChange} t={t} />

      <div className="content-panel">
        <div className="panel-head">
          <h2>{t(`catalog.${activeModule}`)}</h2>
          <FilterBar activeModule={activeModule} cities={cities} filters={filters} loading={loading} onChange={onFilterChange} onLoad={onLoad} t={t} />
        </div>

        <div className="catalog-grid">
          {items.map((item) => (
            <article className="catalog-card" key={`${activeModule}-${item.id}`}>
              <Media item={item} moduleKey={activeModule} />
              <div className="card-body">
                <div>
                  <p className="meta"><MapPin size={14} /> {item.city || item.stadium}</p>
                  <h3>{titleFor(item, activeModule)}</h3>
                  <p>{descriptionFor(item, activeModule, language)}</p>
                </div>
                <CardFacts item={item} moduleKey={activeModule} t={t} />
                <div className="row-actions">
                  <button className="secondary-button" onClick={() => onOpenDetail(item)} type="button">{t('catalog.details')}</button>
                  {currentModule.favoriteType && (
                    <button className="secondary-button" onClick={() => onAddFavorite(item)} type="button">
                      <Heart size={16} /> {t('catalog.addFavorite')}
                    </button>
                  )}
                </div>
              </div>
            </article>
          ))}
          {!items.length && <EmptyState text={t('catalog.empty')} />}
        </div>
      </div>
    </section>
  );
}

function FilterBar({ activeModule, cities, filters, loading, onChange, onLoad, t }) {
  return (
    <div className="filters">
      <select value={filters.city} onChange={(event) => onChange((current) => ({ ...current, city: event.target.value }))}>
        <option value="">{t('catalog.allCities')}</option>
        {cities.map((city) => <option key={city} value={city}>{city}</option>)}
      </select>
      {activeModule === 'matches' && (
        <>
          <input value={filters.group_name} onChange={(event) => onChange((current) => ({ ...current, group_name: event.target.value }))} placeholder={t('catalog.group')} />
          <select value={filters.phase} onChange={(event) => onChange((current) => ({ ...current, phase: event.target.value }))}>
            <option value="">{t('catalog.phase')}</option>
            <option value="group">group</option>
            <option value="round_of_16">round_of_16</option>
            <option value="quarter">quarter</option>
            <option value="semi">semi</option>
            <option value="final">final</option>
          </select>
          <input type="date" value={filters.date} onChange={(event) => onChange((current) => ({ ...current, date: event.target.value }))} />
        </>
      )}
      {activeModule === 'hotels' && (
        <>
          <input type="number" min="1" max="5" value={filters.stars} onChange={(event) => onChange((current) => ({ ...current, stars: event.target.value }))} placeholder={t('catalog.stars')} />
          <input type="number" value={filters.price_min} onChange={(event) => onChange((current) => ({ ...current, price_min: event.target.value }))} placeholder="min MAD" />
          <input type="number" value={filters.price_max} onChange={(event) => onChange((current) => ({ ...current, price_max: event.target.value }))} placeholder="max MAD" />
        </>
      )}
      {activeModule === 'restaurants' && (
        <>
          <input value={filters.search} onChange={(event) => onChange((current) => ({ ...current, search: event.target.value }))} placeholder={t('catalog.cuisine')} />
          <select value={filters.price_range} onChange={(event) => onChange((current) => ({ ...current, price_range: event.target.value }))}>
            <option value="">{t('catalog.priceRange')}</option>
            <option value="budget">budget</option>
            <option value="moyen">moyen</option>
            <option value="gastronomique">gastronomique</option>
          </select>
        </>
      )}
      {activeModule === 'attractions' && (
        <input value={filters.category} onChange={(event) => onChange((current) => ({ ...current, category: event.target.value }))} placeholder={t('catalog.category')} />
      )}
      <button className="primary-button" onClick={onLoad} type="button">{loading ? '...' : t('catalog.load')}</button>
    </div>
  );
}

function DetailView({ item, language, loading, moduleKey, onAddFavorite, onBack, t }) {
  if (loading && !item) return <section className="content-panel"><EmptyState text="..." /></section>;
  if (!item) return <section className="content-panel"><EmptyState text={t('catalog.empty')} /></section>;

  return (
    <section className="detail-layout">
      <button className="secondary-button back-button" onClick={onBack} type="button">{t('catalog.back')}</button>
      <div className="detail-media">
        <Media item={item} moduleKey={moduleKey} />
      </div>
      <article className="content-panel detail-panel">
        <p className="meta"><MapPin size={14} /> {item.city || item.stadium}</p>
        <h2>{titleFor(item, moduleKey)}</h2>
        <p>{descriptionFor(item, moduleKey, language)}</p>
        <CardFacts item={item} moduleKey={moduleKey} t={t} />
        <DetailActions item={item} moduleKey={moduleKey} onAddFavorite={onAddFavorite} t={t} />
      </article>
    </section>
  );
}

function DetailActions({ item, moduleKey, onAddFavorite, t }) {
  return (
    <div className="row-actions detail-actions">
      {moduleKey === 'hotels' && item.website_url && (
        <a className="secondary-button" href={item.website_url} rel="noreferrer" target="_blank"><ExternalLink size={16} />{t('catalog.visitWebsite')}</a>
      )}
      {moduleKey === 'restaurants' && item.phone && (
        <a className="secondary-button" href={`tel:${item.phone}`}><Phone size={16} />{t('catalog.call')}</a>
      )}
      {moduleKey === 'restaurants' && item.whatsapp && (
        <a className="secondary-button" href={`https://wa.me/${item.whatsapp.replace(/\D/g, '')}`} rel="noreferrer" target="_blank"><Phone size={16} />WhatsApp</a>
      )}
      {['hotels', 'restaurants', 'attractions'].includes(moduleKey) && (
        <button className="primary-button" onClick={() => onAddFavorite(item)} type="button"><Heart size={16} />{t('catalog.addFavorite')}</button>
      )}
    </div>
  );
}

function FavoritesView({ favorites, language, onRemove, t }) {
  const groups = [
    ['hotels', favorites.hotels || []],
    ['restaurants', favorites.restaurants || []],
    ['attractions', favorites.attractions || []],
  ];

  return (
    <section className="stack-view">
      {groups.map(([group, items]) => (
        <div className="content-panel" key={group}>
          <div className="panel-head"><h2>{t(`catalog.${group}`)}</h2></div>
          <div className="table-list">
            {items.map((favorite) => (
              <div className="list-row" key={favorite.id}>
                <div>
                  <strong>{favorite.item?.name}</strong>
                  <span>{favorite.item?.city} - {descriptionFor(favorite.item || {}, group, language)}</span>
                </div>
                <button className="danger-button" onClick={() => onRemove(favorite.id)} type="button">
                  <Trash2 size={16} /> {t('catalog.removeFavorite')}
                </button>
              </div>
            ))}
            {!items.length && <EmptyState text={t('catalog.empty')} />}
          </div>
        </div>
      ))}
    </section>
  );
}

function ProfileView({ authForm, authMode, onAuthFormChange, onAuthModeChange, onDemoLogin, onForgotPassword, onProfileChange, onProfileSubmit, onSubmit, profileForm, session, t }) {
  return (
    <section className="profile-grid">
      <div className="content-panel">
        <div className="panel-head">
          <h2>{session.user ? t('nav.profile') : t(`auth.${authMode}`)}</h2>
        </div>
        {session.user ? (
          <form className="form-grid" onSubmit={onProfileSubmit}>
            <div className="profile-card compact-profile">
              {profileForm.avatar_url ? (
                <img className="profile-avatar" src={profileForm.avatar_url} alt={session.user.name} />
              ) : (
                <Shield size={30} />
              )}
              <h3>{session.user.email}</h3>
              <span>{session.user.role}</span>
            </div>
            <input value={profileForm.name} onChange={(event) => onProfileChange((current) => ({ ...current, name: event.target.value }))} placeholder={t('auth.name')} />
            <input value={profileForm.avatar_url} onChange={(event) => onProfileChange((current) => ({ ...current, avatar_url: event.target.value }))} placeholder="avatar_url" />
            <select value={profileForm.preferred_language} onChange={(event) => onProfileChange((current) => ({ ...current, preferred_language: event.target.value }))}>
              <option value="fr">FR</option>
              <option value="en">EN</option>
            </select>
            <button className="primary-button" type="submit">{t('admin.save')}</button>
          </form>
        ) : (
          <form className="form-grid" onSubmit={onSubmit}>
            <div className="segmented">
              <button className={authMode === 'login' ? 'active' : ''} onClick={() => onAuthModeChange('login')} type="button">{t('auth.login')}</button>
              <button className={authMode === 'register' ? 'active' : ''} onClick={() => onAuthModeChange('register')} type="button">{t('auth.register')}</button>
            </div>
            {authMode === 'register' && (
              <input value={authForm.name} onChange={(event) => onAuthFormChange((current) => ({ ...current, name: event.target.value }))} placeholder={t('auth.name')} />
            )}
            <input value={authForm.email} onChange={(event) => onAuthFormChange((current) => ({ ...current, email: event.target.value }))} placeholder={t('auth.email')} />
            <input type="password" value={authForm.password} onChange={(event) => onAuthFormChange((current) => ({ ...current, password: event.target.value, password_confirmation: event.target.value }))} placeholder={t('auth.password')} />
            {authMode === 'register' && (
              <select value={authForm.preferred_language} onChange={(event) => onAuthFormChange((current) => ({ ...current, preferred_language: event.target.value }))}>
                <option value="fr">FR</option>
                <option value="en">EN</option>
              </select>
            )}
            <button className="primary-button" type="submit"><LogIn size={17} /> {t(`auth.${authMode}`)}</button>
            {authMode === 'login' && <button className="secondary-button" onClick={onForgotPassword} type="button">{t('auth.forgotPassword')}</button>}
          </form>
        )}
      </div>

      {!session.user && (
        <div className="content-panel">
          <div className="panel-head"><h2>Demo</h2></div>
          <div className="quick-actions">
            <button className="secondary-button" onClick={() => onDemoLogin('tourist')} type="button">{t('auth.loginAsTourist')}</button>
            <button className="secondary-button" onClick={() => onDemoLogin('admin')} type="button">{t('auth.loginAsAdmin')}</button>
          </div>
        </div>
      )}
    </section>
  );
}

function AdminView({ activeModule, adminForm, catalog, editingId, isAdmin, modules, onDelete, onEdit, onFormChange, onModuleChange, onReset, onSubmit, stats, t, users }) {
  if (!isAdmin) {
    return (
      <section className="content-panel locked">
        <Shield size={40} />
        <h2>Admin only</h2>
        <p>Connectez-vous avec le compte administrateur pour gerer les contenus.</p>
      </section>
    );
  }

  return (
    <section className="workspace">
      <ModuleRail activeModule={activeModule} modules={modules} onModuleChange={onModuleChange} t={t} />
      <div className="admin-grid">
        <div className="content-panel">
          <div className="panel-head"><h2>{t('admin.dashboard')}</h2></div>
          <div className="stats-grid">
            {stats && Object.entries(stats).map(([key, value]) => (
              <div className="stat-tile" key={key}>
                <span>{key}</span>
                <strong>{value}</strong>
              </div>
            ))}
          </div>
          <div className="user-list">
            <h3>{t('admin.users')}</h3>
            {users.map((user) => (
              <div className="compact-row" key={user.id}>
                <span>{user.name}</span>
                <strong>{user.is_active ? user.role : 'disabled'}</strong>
              </div>
            ))}
          </div>
        </div>

        <div className="content-panel">
          <div className="panel-head">
            <h2>{editingId ? t('admin.update') : t('admin.create')} {t(`catalog.${activeModule}`)}</h2>
          </div>
          <AdminForm activeModule={activeModule} form={adminForm} onChange={onFormChange} onReset={onReset} onSubmit={onSubmit} t={t} />
        </div>

        <div className="content-panel wide">
          <div className="panel-head"><h2>{t('admin.content')}</h2></div>
          <div className="table-list">
            {(catalog[activeModule] || []).map((item) => (
              <div className="list-row" key={item.id}>
                <div>
                  <strong>{titleFor(item, activeModule)}</strong>
                  <span>{item.city} {item.status ? `- ${item.status}` : ''}</span>
                </div>
                <div className="row-actions">
                  <button className="secondary-button" onClick={() => onEdit(item)} type="button">{t('admin.update')}</button>
                  <button className="danger-button" onClick={() => onDelete(item)} type="button"><Trash2 size={16} />{t('admin.delete')}</button>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

function AdminForm({ activeModule, form, onChange, onReset, onSubmit, t }) {
  return (
    <form className="form-grid" onSubmit={onSubmit}>
      {Object.keys(form).map((field) => {
        if (Array.isArray(form[field])) {
          return (
            <input
              key={field}
              value={form[field].join(', ')}
              onChange={(event) => onChange((current) => ({ ...current, [field]: event.target.value.split(',').map((value) => value.trim()) }))}
              placeholder={field}
            />
          );
        }

        if (field === 'phase') return <SelectField key={field} field={field} form={form} onChange={onChange} options={['group', 'round_of_16', 'quarter', 'semi', 'final']} />;
        if (field === 'status') return <SelectField key={field} field={field} form={form} onChange={onChange} options={['upcoming', 'live', 'finished']} />;
        if (field === 'price_range') return <SelectField key={field} field={field} form={form} onChange={onChange} options={['budget', 'moyen', 'gastronomique']} />;

        return (
          <input
            key={field}
            type={field.includes('date') ? 'date' : field.includes('time') ? 'time' : 'text'}
            value={form[field] ?? ''}
            onChange={(event) => onChange((current) => ({ ...current, [field]: numericField(field) ? Number(event.target.value) : event.target.value }))}
            placeholder={field}
          />
        );
      })}
      <div className="form-actions">
        <button className="primary-button" type="submit"><Plus size={16} /> {t('admin.save')}</button>
        <button className="secondary-button" onClick={onReset} type="button">{t('admin.reset')}</button>
      </div>
    </form>
  );
}

function SelectField({ field, form, onChange, options }) {
  return (
    <select value={form[field]} onChange={(event) => onChange((current) => ({ ...current, [field]: event.target.value }))}>
      {options.map((option) => <option key={option} value={option}>{option}</option>)}
    </select>
  );
}

function ModuleRail({ activeModule, modules, onModuleChange, t }) {
  return (
    <aside className="module-rail">
      {modules.map(({ key, icon: Icon }) => (
        <button className={activeModule === key ? 'active' : ''} key={key} onClick={() => onModuleChange(key)} type="button">
          <Icon size={20} />
          <span>{t(`catalog.${key}`)}</span>
        </button>
      ))}
    </aside>
  );
}

function Media({ item, moduleKey }) {
  const image = item.photos?.[0];
  if (image) return <img className="media" src={image} alt={titleFor(item, moduleKey)} />;
  if (moduleKey === 'matches' && (item.team_home_flag_url || item.team_away_flag_url)) {
    return (
      <div className="media match-media">
        <TeamFlag src={item.team_home_flag_url} code={item.team_home_code} name={item.team_home} />
        <span>vs</span>
        <TeamFlag src={item.team_away_flag_url} code={item.team_away_code} name={item.team_away} />
      </div>
    );
  }

  return (
    <div className="media fallback">
      <CalendarDays size={28} />
      <span>{item.match_date || item.city}</span>
    </div>
  );
}

function TeamFlag({ src, code, name }) {
  return (
    <div className="team-flag">
      {src ? <img src={src} alt={`${name} flag`} /> : <Trophy size={28} />}
      <strong>{code || name}</strong>
    </div>
  );
}

function CardFacts({ item, moduleKey, t }) {
  if (moduleKey === 'matches') return <div className="facts"><span>{item.match_date}</span><span>{item.match_time}</span><span>{t(`status.${item.status}`)}</span></div>;
  if (moduleKey === 'hotels') return <div className="facts"><span>{item.stars} stars</span><span>{item.price_min}-{item.price_max} {item.currency}</span></div>;
  if (moduleKey === 'restaurants') return <div className="facts"><span>{item.cuisine_type}</span><span>{item.price_range}</span></div>;
  return <div className="facts"><span>{item.category}</span><span>{item.entry_price ?? 0} MAD</span></div>;
}

function EmptyState({ text }) {
  return <div className="empty-state"><Check size={24} /><span>{text}</span></div>;
}

function titleFor(item, moduleKey) {
  if (moduleKey === 'matches') return `${item.team_home} vs ${item.team_away}`;
  return item.name;
}

function descriptionFor(item, moduleKey, language) {
  if (moduleKey === 'matches') return `${item.stadium || ''} ${item.group_name ? `- Group ${item.group_name}` : ''}`;
  return language === 'en' ? item.description_en : item.description_fr;
}

function numericField(field) {
  return ['stars', 'price_min', 'price_max', 'entry_price', 'score_home', 'score_away'].includes(field);
}

function buildParams(moduleKey, filters) {
  const params = {};
  ['city', 'search', 'group_name', 'phase', 'date', 'stars', 'price_min', 'price_max', 'price_range', 'category'].forEach((key) => {
    if (filters[key]) params[key === 'date' ? 'match_date' : key] = filters[key];
  });

  if (moduleKey === 'matches') return pick(params, ['city', 'group_name', 'phase', 'match_date']);
  if (moduleKey === 'hotels') return pick(params, ['city', 'stars', 'price_min', 'price_max']);
  if (moduleKey === 'restaurants') return pick(params, ['city', 'search', 'price_range']);
  if (moduleKey === 'attractions') return pick(params, ['city', 'category']);
  return {};
}

function pick(source, keys) {
  return keys.reduce((result, key) => {
    if (source[key]) result[key] = source[key];
    return result;
  }, {});
}

export default App;
