import { Heart, Home, Hotel, Languages, LogIn, LogOut, User } from 'lucide-react';

export function AppHeader({ changeLanguage, i18n, navigate, onLogout, publicActive, route, session, activeModule, modules, t }) {
  return (
    <header className="topbar">
      <button className="brand" onClick={() => navigate('/')} type="button">
        <img className="brand-mark" src="/assets/maghrebpass-logo.png" alt="" />
        <span>{t('appName')}</span>
      </button>

      <nav className="nav-tabs" aria-label={t('a11y.mainNavigation')}>
        <button aria-current={route.view === 'home' ? 'page' : undefined} className={route.view === 'home' ? 'active' : ''} onClick={() => navigate('/')} type="button">
          <Home size={16} />
          {t('nav.home')}
        </button>
        {modules.map((module) => {
          const Icon = module.icon;
          const active = publicActive && activeModule === module.key;
          return (
            <button aria-current={active ? 'page' : undefined} className={active ? 'active' : ''} key={module.key} onClick={() => navigate(module.basePath)} type="button">
              <Icon size={16} />
              {t(`catalog.${module.key}`)}
            </button>
          );
        })}
        <button aria-current={route.view === 'favorites' ? 'page' : undefined} className={route.view === 'favorites' ? 'active' : ''} onClick={() => navigate('/favorites')} type="button">{t('nav.favorites')}</button>
        <button aria-current={route.view === 'profile' ? 'page' : undefined} className={route.view === 'profile' ? 'active' : ''} onClick={() => navigate(session.user ? '/profile' : '/login')} type="button"><User size={16} />{t('nav.profile')}</button>
      </nav>

      <div className="header-actions">
        <button aria-label={t('a11y.changeLanguage')} className="icon-button" onClick={() => changeLanguage(i18n.language === 'fr' ? 'en' : 'fr')} title={t('a11y.changeLanguage')} type="button">
          <Languages size={18} />
          <span>{i18n.language.toUpperCase()}</span>
        </button>
        {session.user ? (
          <button className="ghost-button" onClick={onLogout} type="button"><LogOut size={17} />{t('auth.logout')}</button>
        ) : (
          <button className="ghost-button" onClick={() => navigate('/login')} type="button"><LogIn size={17} />{t('auth.login')}</button>
        )}
      </div>
    </header>
  );
}

export function HeroBand({ navigate, session, t }) {
  return (
    <section className="hero-band">
      <div className="hero-copy">
        <p className="eyebrow">World Cup Morocco 2030</p>
        <h1>{t('tagline')}</h1>
        <div className="hero-actions">
          <button className="primary-button" onClick={() => navigate('/matches')} type="button">{t('catalog.matches')}</button>
          <button className="secondary-button" onClick={() => navigate('/hotels')} type="button"><Hotel size={16} />{t('catalog.hotels')}</button>
        </div>
      </div>
      <div className="hero-status">
        <span>{session.user ? session.user.name : 'Guest'}</span>
        <strong>{session.user?.role || 'visitor'}</strong>
      </div>
    </section>
  );
}

export function ExperienceCta({ session, navigate, t }) {
  return (
    <section className="experience-cta">
      <div>
        <p className="eyebrow">MaghrebPass</p>
        <h2>{session.user ? t('cta.authTitle') : t('cta.guestTitle')}</h2>
        <p>{t('cta.body')}</p>
      </div>
      <div className="cta-actions">
        {!session.user ? (
          <>
            <button className="cta-light" onClick={() => navigate('/register')} type="button"><LogIn size={17} />{t('auth.register')}</button>
            <button className="cta-outline" onClick={() => navigate('/login')} type="button">{t('auth.login')}</button>
          </>
        ) : (
          <button className="cta-light" onClick={() => navigate('/favorites')} type="button"><Heart size={17} />{t('nav.favorites')}</button>
        )}
      </div>
    </section>
  );
}

export function AppFooter({ changeLanguage, i18n, navigate, t }) {
  return (
    <footer className="site-footer">
      <div className="footer-grid">
        <div className="footer-brand">
          <button className="brand footer-logo" onClick={() => navigate('/')} type="button">
            <img className="brand-mark" src="/assets/maghrebpass-logo.png" alt="" />
            <span>{t('appName')}</span>
          </button>
          <p>{t('footer.description')}</p>
        </div>
        <div>
          <h3>{t('footer.links')}</h3>
          <button onClick={() => navigate('/matches')} type="button">{t('catalog.matches')}</button>
          <button onClick={() => navigate('/hotels')} type="button">{t('catalog.hotels')}</button>
          <button onClick={() => navigate('/restaurants')} type="button">{t('catalog.restaurants')}</button>
          <button onClick={() => navigate('/attractions')} type="button">{t('catalog.attractions')}</button>
        </div>
        <div>
          <h3>{t('footer.information')}</h3>
          <span>{t('footer.faq')}</span>
          <span>{t('footer.terms')}</span>
          <span>{t('footer.privacy')}</span>
        </div>
        <div>
          <h3>{t('footer.contact')}</h3>
          <span>contact@maghrebpass.com</span>
          <span>+212 5 29 12 34 56</span>
          <span>{t('footer.location')}</span>
        </div>
        <div>
          <h3>{t('footer.language')}</h3>
          <div className="footer-language">
            <button aria-pressed={i18n.language === 'fr'} className={i18n.language === 'fr' ? 'active' : ''} onClick={() => changeLanguage('fr')} type="button">FR</button>
            <button aria-pressed={i18n.language === 'en'} className={i18n.language === 'en' ? 'active' : ''} onClick={() => changeLanguage('en')} type="button">EN</button>
          </div>
        </div>
      </div>
      <div className="footer-bottom">{t('footer.copyright')}</div>
    </footer>
  );
}
