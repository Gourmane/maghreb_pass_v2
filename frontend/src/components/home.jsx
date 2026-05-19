import { ArrowRight, CalendarCheck, CalendarDays, Globe2, Heart, Hotel, MapPin, Search, ShieldCheck, Sparkles, Trophy, Users, Utensils } from 'lucide-react';
import { CardFacts, Media } from './common.jsx';
import { descriptionFor, modules, titleFor } from '../lib/catalog.js';

const sectionConfig = {
  matches: { limit: 4, className: 'home-match-list' },
  hotels: { limit: 4, className: 'home-card-grid' },
  restaurants: { limit: 4, className: 'home-card-grid' },
  attractions: { limit: 4, className: 'home-card-grid' },
};

const reasonIcons = [ShieldCheck, Globe2, Heart, Trophy];

export function HomePage({ catalog, language, loading, navigate, onAddFavorite, session, t }) {
  return (
    <section className="home-page">
      <HomeHero navigate={navigate} t={t} />
      <HomeSearchBar t={t} />
      <HomeModuleTiles navigate={navigate} t={t} />
      <WhyChoose t={t} />
      {modules.map((module) => (
        <HomePreviewSection
          catalog={catalog}
          key={module.key}
          language={language}
          loading={loading}
          module={module}
          navigate={navigate}
          onAddFavorite={onAddFavorite}
          t={t}
        />
      ))}
      <HomeSteps t={t} />
      <HomeCta navigate={navigate} session={session} t={t} />
    </section>
  );
}

function HomeHero({ navigate, t }) {
  return (
    <section className="home-hero">
      <div className="home-hero-copy">
        <p className="eyebrow">{t('home.eyebrow')}</p>
        <h1>{t('home.heroTitleLead')} <span>{t('home.heroTitleAccent')}</span></h1>
        <p>{t('home.heroBody')}</p>
        <div className="hero-actions">
          <button className="primary-button" onClick={() => navigate('/matches')} type="button">{t('home.discover')}</button>
          <button className="secondary-button" onClick={() => navigate('/matches')} type="button"><CalendarCheck size={16} />{t('home.exploreMatches')}</button>
        </div>
      </div>
      <div className="home-hero-visual" aria-hidden="true">
        <div className="hero-ribbon" />
      </div>
    </section>
  );
}

function HomeSearchBar({ t }) {
  const fields = [
    { icon: MapPin, title: t('catalog.city'), value: t('catalog.allCities') },
    { icon: CalendarDays, title: t('catalog.date'), value: 'Selectionner' },
    { icon: Utensils, title: t('catalog.category'), value: 'Hotels, Restos, etc.' },
    { icon: Users, title: 'Invites', value: '2 adultes' },
  ];

  return (
    <div className="home-search-shell" aria-label={t('catalog.filters')}>
      {fields.map(({ icon: Icon, title, value }) => (
        <div className="home-search-field" key={title}>
          <span><Icon size={22} /></span>
          <div>
            <strong>{title}</strong>
            <small>{value}</small>
          </div>
        </div>
      ))}
      <div className="home-search-submit" aria-label={t('catalog.search')}>
        <Search size={23} />
      </div>
    </div>
  );
}

function HomeModuleTiles({ navigate, t }) {
  return (
    <div className="home-module-tiles">
      {modules.map(({ key, icon: Icon, basePath }) => (
        <button key={key} onClick={() => navigate(basePath)} type="button">
          <span><Icon size={30} /></span>
          <strong>{t(`catalog.${key}`)}</strong>
          <small>{t(`home.moduleSub.${key}`)}</small>
        </button>
      ))}
    </div>
  );
}

function WhyChoose({ t }) {
  return (
    <section className="home-section">
      <div className="home-section-head centered">
        <h2>{t('home.whyTitle')}</h2>
      </div>
      <div className="home-reasons">
        {['centralized', 'bilingual', 'favorites', 'culture'].map((key, index) => {
          const Icon = reasonIcons[index];
          return (
            <article key={key}>
              <span><Icon size={24} /></span>
              <h3>{t(`home.reasons.${key}.title`)}</h3>
              <p>{t(`home.reasons.${key}.body`)}</p>
            </article>
          );
        })}
      </div>
    </section>
  );
}

function HomePreviewSection({ catalog, language, loading, module, navigate, onAddFavorite, t }) {
  const items = (catalog[module.key] || []).slice(0, sectionConfig[module.key].limit);
  const isMatches = module.key === 'matches';

  return (
    <section className="home-section">
      <div className="home-section-head">
        <h2>{t(`home.sections.${module.key}`)}</h2>
        <button className="section-link" onClick={() => navigate(module.basePath)} type="button">
          {t(`home.viewAll.${module.key}`)}
          <ArrowRight size={15} />
        </button>
      </div>

      <div className={sectionConfig[module.key].className}>
        {items.map((item) => (
          isMatches ? (
            <button className="home-match-row" key={`${module.key}-${item.id}`} onClick={() => navigate(`${module.basePath}/${item.id}`)} type="button">
              <span className="match-date">{item.match_date}</span>
              <div className="home-match-teams">
                <TeamBadge code={item.team_home_code} flag={item.team_home_flag_url} name={item.team_home} />
                <span className="match-versus">vs</span>
                <TeamBadge code={item.team_away_code} flag={item.team_away_flag_url} name={item.team_away} />
              </div>
              <small>{item.stadium}</small>
              <em>{item.match_time}</em>
              <ArrowRight size={17} />
            </button>
          ) : (
            <article className="home-preview-card" key={`${module.key}-${item.id}`}>
              <Media item={item} moduleKey={module.key} />
              <div>
                <h3>{titleFor(item, module.key)}</h3>
                <p>{descriptionFor(item, module.key, language)}</p>
                <CardFacts item={item} moduleKey={module.key} t={t} />
                <div className="row-actions">
                  <button className="secondary-button" disabled={loading} onClick={() => navigate(`${module.basePath}/${item.id}`)} type="button">{t('catalog.details')}</button>
                  <button className="icon-favorite" aria-label={t('catalog.addFavorite')} disabled={loading} onClick={() => onAddFavorite(item)} type="button"><Heart size={17} /></button>
                </div>
              </div>
            </article>
          )
        ))}
      </div>
    </section>
  );
}

function TeamBadge({ code, flag, name }) {
  return (
    <span className="home-team-badge">
      {flag ? <img src={flag} alt="" loading="lazy" decoding="async" /> : <Trophy size={18} />}
      <strong>{name || code}</strong>
    </span>
  );
}

function HomeSteps({ t }) {
  const icons = [CalendarCheck, Hotel, Sparkles];
  return (
    <section className="home-section">
      <div className="home-section-head centered">
        <h2>{t('home.stepsTitle')}</h2>
      </div>
      <div className="home-steps">
        {['matches', 'stay', 'enjoy'].map((key, index) => {
          const Icon = icons[index];
          return (
            <article key={key}>
              <span>{index + 1}</span>
              <Icon size={30} />
              <h3>{t(`home.steps.${key}.title`)}</h3>
              <p>{t(`home.steps.${key}.body`)}</p>
            </article>
          );
        })}
      </div>
    </section>
  );
}

function HomeCta({ navigate, session, t }) {
  return (
    <section className="home-cta">
      <div>
        <h2>{session.user ? t('cta.authTitle') : t('cta.guestTitle')}</h2>
        <p>{t('cta.body')}</p>
      </div>
      <div className="cta-actions">
        {!session.user ? (
          <>
            <button className="cta-light" onClick={() => navigate('/register')} type="button"><LogInIcon />{t('auth.register')}</button>
            <button className="cta-outline" onClick={() => navigate('/login')} type="button">{t('auth.login')}</button>
          </>
        ) : (
          <button className="cta-light" onClick={() => navigate('/favorites')} type="button"><Heart size={17} />{t('nav.favorites')}</button>
        )}
      </div>
    </section>
  );
}

function LogInIcon() {
  return <Sparkles size={17} />;
}
