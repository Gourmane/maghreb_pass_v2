import { ExternalLink, Heart, MapPin, Phone, Trash2 } from 'lucide-react';
import { CardFacts, EmptyState, Field, Media, ModuleRail } from './common.jsx';
import { descriptionFor, titleFor } from '../lib/catalog.js';

export function PublicCatalog({ activeModule, catalog, cities, currentModule, filters, language, loading, modules, onAddFavorite, onFilterChange, onLoad, onModuleChange, onOpenDetail, t }) {
  const items = catalog[activeModule] || [];

  return (
    <section className={`workspace ${activeModule === 'matches' ? 'matches-workspace' : ''}`}>
      <ModuleRail activeModule={activeModule} modules={modules} onModuleChange={onModuleChange} t={t} />

      <div className="content-panel">
        <div className="panel-head">
          <div>
            <p className="section-kicker">{activeModule === 'matches' ? t('catalog.officialSchedule') : t('catalog.popularSelections')}</p>
            <h2>{t(`catalog.${activeModule}`)}</h2>
          </div>
          <FilterBar activeModule={activeModule} cities={cities} filters={filters} loading={loading} onChange={onFilterChange} onLoad={onLoad} t={t} />
        </div>

        <div className={`catalog-grid ${activeModule === 'matches' ? 'match-list' : ''}`}>
          {items.map((item) => (
            <article className={`catalog-card ${activeModule === 'matches' ? 'match-card' : ''}`} key={`${activeModule}-${item.id}`}>
              <Media item={item} moduleKey={activeModule} />
              <div className="card-body">
                <div>
                  <p className="meta"><MapPin size={14} /> {item.city || item.stadium}</p>
                  <h3>{titleFor(item, activeModule)}</h3>
                  <p>{descriptionFor(item, activeModule, language)}</p>
                </div>
                <CardFacts item={item} moduleKey={activeModule} t={t} />
                <div className="row-actions">
                  <button className="secondary-button" disabled={loading} onClick={() => onOpenDetail(item)} type="button">{t('catalog.details')}</button>
                  {currentModule.favoriteType && (
                    <button className="secondary-button" disabled={loading} onClick={() => onAddFavorite(item)} type="button">
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
    <div aria-label={t('catalog.filters')} className="filters" role="group">
      <Field id="filter-city" label={t('catalog.city')} compact>
        <select id="filter-city" value={filters.city} onChange={(event) => onChange((current) => ({ ...current, city: event.target.value }))}>
          <option value="">{t('catalog.allCities')}</option>
          {cities.map((city) => <option key={city} value={city}>{city}</option>)}
        </select>
      </Field>
      {activeModule === 'matches' && (
        <>
          <Field id="filter-group" label={t('catalog.group')} compact>
            <input id="filter-group" value={filters.group_name} onChange={(event) => onChange((current) => ({ ...current, group_name: event.target.value }))} placeholder={t('catalog.group')} />
          </Field>
          <Field id="filter-phase" label={t('catalog.phase')} compact>
            <select id="filter-phase" value={filters.phase} onChange={(event) => onChange((current) => ({ ...current, phase: event.target.value }))}>
              <option value="">{t('catalog.phase')}</option>
              <option value="group">group</option>
              <option value="round_of_16">round_of_16</option>
              <option value="quarter">quarter</option>
              <option value="semi">semi</option>
              <option value="final">final</option>
            </select>
          </Field>
          <Field id="filter-date" label={t('catalog.date')} compact>
            <input id="filter-date" type="date" value={filters.date} onChange={(event) => onChange((current) => ({ ...current, date: event.target.value }))} />
          </Field>
        </>
      )}
      {activeModule === 'hotels' && (
        <>
          <Field id="filter-stars" label={t('catalog.stars')} compact>
            <input id="filter-stars" type="number" min="1" max="5" value={filters.stars} onChange={(event) => onChange((current) => ({ ...current, stars: event.target.value }))} placeholder={t('catalog.stars')} />
          </Field>
          <Field id="filter-price-min" label={t('catalog.minPrice')} compact>
            <input id="filter-price-min" type="number" min="0" value={filters.price_min} onChange={(event) => onChange((current) => ({ ...current, price_min: event.target.value }))} placeholder={t('catalog.minPrice')} />
          </Field>
          <Field id="filter-price-max" label={t('catalog.maxPrice')} compact>
            <input id="filter-price-max" type="number" min="0" value={filters.price_max} onChange={(event) => onChange((current) => ({ ...current, price_max: event.target.value }))} placeholder={t('catalog.maxPrice')} />
          </Field>
        </>
      )}
      {activeModule === 'restaurants' && (
        <>
          <Field id="filter-cuisine" label={t('catalog.cuisine')} compact>
            <input id="filter-cuisine" value={filters.search} onChange={(event) => onChange((current) => ({ ...current, search: event.target.value }))} placeholder={t('catalog.cuisine')} />
          </Field>
          <Field id="filter-price-range" label={t('catalog.priceRange')} compact>
            <select id="filter-price-range" value={filters.price_range} onChange={(event) => onChange((current) => ({ ...current, price_range: event.target.value }))}>
              <option value="">{t('catalog.priceRange')}</option>
              <option value="budget">budget</option>
              <option value="moyen">moyen</option>
              <option value="gastronomique">gastronomique</option>
            </select>
          </Field>
        </>
      )}
      {activeModule === 'attractions' && (
        <Field id="filter-category" label={t('catalog.category')} compact>
          <input id="filter-category" value={filters.category} onChange={(event) => onChange((current) => ({ ...current, category: event.target.value }))} placeholder={t('catalog.category')} />
        </Field>
      )}
      <button aria-busy={loading} className="primary-button" disabled={loading} onClick={onLoad} type="button">{loading ? t('messages.loading') : t('catalog.load')}</button>
    </div>
  );
}

export function DetailView({ item, language, loading, moduleKey, onAddFavorite, onBack, t }) {
  if (loading && !item) return <section className="content-panel"><EmptyState text={t('messages.loading')} /></section>;
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

export function FavoritesView({ favorites, language, onRemove, t }) {
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
