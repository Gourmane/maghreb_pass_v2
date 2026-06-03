import 'leaflet/dist/leaflet.css';
import { CircleMarker, MapContainer, Popup, TileLayer } from 'react-leaflet';
import { CalendarPlus, ExternalLink, Heart, MapPin, Phone, Trash2 } from 'lucide-react';
import { CardFacts, EmptyState, Field, Media, ModuleRail } from './common.jsx';
import { descriptionFor, titleFor } from '../lib/catalog.js';
import { ReservationForm } from './reservation-form.jsx';

export function PublicCatalog({ activeModule, catalog, cities, currentModule, filterOptions, filters, language, loading, modules, onAddFavorite, onFilterChange, onLoad, onModuleChange, onOpenDetail, t }) {
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
          <FilterBar activeModule={activeModule} cities={cities} filterOptions={filterOptions} filters={filters} loading={loading} onChange={onFilterChange} onLoad={onLoad} t={t} />
        </div>

        <div className={`catalog-grid ${activeModule === 'matches' ? 'match-list' : ''}`}>
          {items.map((item) => (
            <article className={`catalog-card ${activeModule === 'matches' ? 'match-card' : ''}`} key={`${activeModule}-${item.id}`}>
              <Media item={item} moduleKey={activeModule} />
              <div className="card-body">
                <div>
                  <p className="meta"><MapPin size={14} /> {item.city || item.stadium}</p>
                  <h3>{titleFor(item, activeModule, language)}</h3>
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

function FilterBar({ activeModule, cities, filterOptions, filters, loading, onChange, onLoad, t }) {
  const matchOptions = filterOptions.matches || { group_names: [], phases: [] };
  const hotelOptions = filterOptions.hotels || { stars: [] };
  const restaurantOptions = filterOptions.restaurants || { cuisine_types: [], price_ranges: [] };
  const attractionOptions = filterOptions.attractions || { categories: [] };

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
            <select id="filter-group" value={filters.group_name} onChange={(event) => onChange((current) => ({ ...current, group_name: event.target.value }))}>
              <option value="">{t('catalog.allGroups')}</option>
              {matchOptions.group_names.map((group) => <option key={group} value={group}>{group}</option>)}
            </select>
          </Field>
          <Field id="filter-phase" label={t('catalog.phase')} compact>
            <select id="filter-phase" value={filters.phase} onChange={(event) => onChange((current) => ({ ...current, phase: event.target.value }))}>
              <option value="">{t('catalog.allPhases')}</option>
              {(matchOptions.phases.length ? matchOptions.phases : ['group', 'round_of_16', 'quarter', 'semi', 'final']).map((phase) => (
                <option key={phase} value={phase}>{t(`options.phase.${phase}`)}</option>
              ))}
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
            <select id="filter-stars" value={filters.stars} onChange={(event) => onChange((current) => ({ ...current, stars: event.target.value }))}>
              <option value="">{t('catalog.allStars')}</option>
              {(hotelOptions.stars.length ? hotelOptions.stars : [1, 2, 3, 4, 5]).map((star) => (
                <option key={star} value={star}>{star} {t('common.stars')}</option>
              ))}
            </select>
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
            <select id="filter-cuisine" value={filters.cuisine} onChange={(event) => onChange((current) => ({ ...current, cuisine: event.target.value }))}>
              <option value="">{t('catalog.allCuisines')}</option>
              {restaurantOptions.cuisine_types.map((cuisine) => <option key={cuisine} value={cuisine}>{cuisine}</option>)}
            </select>
          </Field>
          <Field id="filter-price-range" label={t('catalog.priceRange')} compact>
            <select id="filter-price-range" value={filters.price_range} onChange={(event) => onChange((current) => ({ ...current, price_range: event.target.value }))}>
              <option value="">{t('catalog.allPriceRanges')}</option>
              {(restaurantOptions.price_ranges.length ? restaurantOptions.price_ranges : ['budget', 'moyen', 'gastronomique']).map((range) => (
                <option key={range} value={range}>{t(`options.priceRange.${range}`, { defaultValue: range })}</option>
              ))}
            </select>
          </Field>
        </>
      )}
      {activeModule === 'attractions' && (
        <Field id="filter-category" label={t('catalog.category')} compact>
          <select id="filter-category" value={filters.category} onChange={(event) => onChange((current) => ({ ...current, category: event.target.value }))}>
            <option value="">{t('catalog.allCategories')}</option>
            {attractionOptions.categories.map((category) => <option key={category} value={category}>{category}</option>)}
          </select>
        </Field>
      )}
      {activeModule === 'packages' && (
        <Field id="filter-search" label={t('catalog.search')} compact>
          <input id="filter-search" value={filters.search} onChange={(event) => onChange((current) => ({ ...current, search: event.target.value }))} placeholder={t('catalog.search')} />
        </Field>
      )}
      <button aria-busy={loading} className="primary-button" disabled={loading} onClick={onLoad} type="button">{loading ? t('messages.loading') : t('catalog.load')}</button>
    </div>
  );
}

export function DetailView({ item, language, loading, matchNearby, moduleKey, navigate, onAddFavorite, onBack, onOpenNearby, session, t }) {
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
        <h2>{titleFor(item, moduleKey, language)}</h2>
        <p>{descriptionFor(item, moduleKey, language)}</p>
        <CardFacts item={item} moduleKey={moduleKey} t={t} />
        <DetailActions item={item} moduleKey={moduleKey} onAddFavorite={onAddFavorite} t={t} />
      </article>
      <DetailMiniMap item={item} moduleKey={moduleKey} t={t} />
      {moduleKey === 'packages' && <PackageTimeline item={item} language={language} t={t} />}
      {moduleKey === 'matches' && <MatchNearby nearby={matchNearby} language={language} onOpenNearby={onOpenNearby} t={t} />}
      <ReservationForm item={item} moduleKey={moduleKey} navigate={navigate} session={session} t={t} />
    </section>
  );
}

function DetailMiniMap({ item, moduleKey, t }) {
  if (!['hotels', 'restaurants', 'attractions'].includes(moduleKey)) return null;

  const latitude = Number(item.latitude);
  const longitude = Number(item.longitude);

  if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return null;

  return (
    <section className="content-panel detail-map-panel">
      <div className="panel-head">
        <div>
          <p className="section-kicker">{item.city}</p>
          <h2>{t('catalog.location')}</h2>
        </div>
        {item.map_url && (
          <a className="secondary-button" href={item.map_url} rel="noreferrer" target="_blank">
            <ExternalLink size={16} />{t('catalog.openMap')}
          </a>
        )}
      </div>
      <div className="detail-mini-map" aria-label={t('catalog.location')}>
        <MapContainer center={[latitude, longitude]} zoom={15} scrollWheelZoom={false} dragging={false} className="leaflet-map">
          <TileLayer
            attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
          />
          <CircleMarker
            center={[latitude, longitude]}
            pathOptions={{ color: '#0b6b43', fillColor: '#c90d16', fillOpacity: 0.82, weight: 2 }}
            radius={10}
          >
            <Popup>{item.name}</Popup>
          </CircleMarker>
        </MapContainer>
      </div>
    </section>
  );
}

function MatchNearby({ language, nearby, onOpenNearby, t }) {
  const groups = [
    ['hotels', nearby?.hotels || []],
    ['restaurants', nearby?.restaurants || []],
    ['attractions', nearby?.attractions || []],
    ['packages', nearby?.packages || []],
  ];
  const hasItems = groups.some(([, items]) => items.length);

  return (
    <section className="content-panel match-nearby">
      <div className="panel-head">
        <div>
          <p className="section-kicker">{nearby?.city || t('catalog.city')}</p>
          <h2>{t('nearby.title')}</h2>
        </div>
      </div>
      {hasItems ? (
        <div className="nearby-grid">
          {groups.map(([group, items]) => (
            <div className="nearby-column" key={group}>
              <h3>{t(`catalog.${group}`)}</h3>
              <div className="table-list">
                {items.map((nearbyItem) => (
                  <div className="list-row nearby-row" key={`${group}-${nearbyItem.id}`}>
                    <div>
                      <strong>{titleFor(nearbyItem, group, language)}</strong>
                      <span>{descriptionFor(nearbyItem, group, language) || nearbyItem.city}</span>
                    </div>
                    <button className="secondary-button" onClick={() => onOpenNearby(group, nearbyItem)} type="button">
                      {t('catalog.details')}
                    </button>
                  </div>
                ))}
                {!items.length && <EmptyState text={t('catalog.empty')} />}
              </div>
            </div>
          ))}
        </div>
      ) : (
        <EmptyState text={t('nearby.empty')} />
      )}
    </section>
  );
}

function PackageTimeline({ item, language, t }) {
  const grouped = (item.items || []).reduce((days, packageItem) => {
    const day = packageItem.day_number || 1;
    days[day] ||= [];
    days[day].push(packageItem);
    return days;
  }, {});

  return (
    <section className="content-panel package-timeline">
      <div className="panel-head"><h2>{t('packages.timeline')}</h2></div>
      {Object.entries(grouped).map(([day, items]) => (
        <div className="package-day" key={day}>
          <strong>{t('packages.day')} {day}</strong>
          <div className="table-list">
            {items.map((packageItem) => {
              const summary = packageItem.item || {};
              const title = language === 'en'
                ? summary.title_en || summary.title || packageItem.custom_title || t('catalog.empty')
                : summary.title_fr || summary.title || packageItem.custom_title || t('catalog.empty');
              const description = language === 'en' ? summary.description_en : summary.description_fr;
              return (
                <div className="list-row" key={packageItem.id}>
                  <div>
                    <strong>{title}</strong>
                    <span>{t(`options.itemType.${packageItem.item_type}`, { defaultValue: packageItem.item_type })} - {description || summary.description || packageItem.custom_description || ''}</span>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      ))}
      {!item.items?.length && <EmptyState text={t('catalog.empty')} />}
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

export function FavoritesView({ favorites, language, navigate, onRemove, session, t }) {
  const groups = [
    ['hotels', favorites.hotels || []],
    ['restaurants', favorites.restaurants || []],
    ['attractions', favorites.attractions || []],
  ];
  const total = groups.reduce((count, [, items]) => count + items.length, 0);

  if (!session.user) {
    return (
      <section className="content-panel locked">
        <h2>{t('catalog.favoritesAuthTitle')}</h2>
        <p>{t('catalog.favoritesAuthBody')}</p>
        <div className="form-actions">
          <button className="primary-button" onClick={() => navigate('/login')} type="button">{t('auth.login')}</button>
          <button className="secondary-button" onClick={() => navigate('/register')} type="button">{t('auth.register')}</button>
        </div>
      </section>
    );
  }

  return (
    <section className="stack-view">
      <div className="content-panel favorites-planner-cta">
        <div>
          <p className="section-kicker">{t('nav.favorites')}</p>
          <h2>{t('catalog.planFavoritesTitle')}</h2>
          <p>{t('catalog.planFavoritesBody')}</p>
        </div>
        <button className="primary-button" disabled={!total} onClick={() => navigate('/trip-planner')} type="button">
          <CalendarPlus size={16} />{t('catalog.openPlanner')}
        </button>
      </div>
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
