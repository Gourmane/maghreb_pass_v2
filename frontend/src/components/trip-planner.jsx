import { CalendarPlus, Clock, Heart, NotebookPen, Plus, Trash2 } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { EmptyState, Field } from './common.jsx';
import { api, unwrapPage } from '../lib/api.js';
import { descriptionFor, modules, titleFor } from '../lib/catalog.js';

const blankTrip = {
  title: '',
  city: '',
  start_date: '',
  end_date: '',
  notes: '',
};

const blankItem = {
  item_type: 'custom',
  item_id: '',
  custom_title: '',
  custom_description: '',
  day_number: 1,
  start_time: '',
  notes: '',
};

const typeToModule = {
  hotel: 'hotels',
  restaurant: 'restaurants',
  attraction: 'attractions',
  match: 'matches',
  package: 'packages',
};

const moduleToType = Object.fromEntries(Object.entries(typeToModule).map(([type, moduleKey]) => [moduleKey, type]));

export function TripPlannerView({ language, loading, navigate, routeTripId, session, t }) {
  const [trips, setTrips] = useState([]);
  const [selectedTripId, setSelectedTripId] = useState('');
  const [tripForm, setTripForm] = useState(blankTrip);
  const [itemForm, setItemForm] = useState(blankItem);
  const [editingItemId, setEditingItemId] = useState(null);
  const [catalog, setCatalog] = useState({});
  const [favorites, setFavorites] = useState({ hotels: [], restaurants: [], attractions: [] });
  const [busy, setBusy] = useState(false);
  const [localMessage, setLocalMessage] = useState('');
  const [localError, setLocalError] = useState('');

  const selectedTrip = useMemo(
    () => trips.find((trip) => String(trip.id) === String(selectedTripId)) || trips[0] || null,
    [selectedTripId, trips],
  );

  const selectedItemValue = itemForm.item_type === 'custom' || !itemForm.item_id ? '' : `${itemForm.item_type}:${itemForm.item_id}`;
  const dayGroups = useMemo(() => groupItemsByDay(selectedTrip?.items || []), [selectedTrip]);
  const catalogOptions = useMemo(() => buildCatalogOptions(catalog, language, t), [catalog, language, t]);
  const favoriteOptions = useMemo(() => buildFavoriteOptions(favorites, language, t), [favorites, language, t]);

  useEffect(() => {
    if (session.user) {
      loadPlanner();
    }
  }, [session.user?.id]);

  useEffect(() => {
    if (selectedTrip && !selectedTripId) setSelectedTripId(String(selectedTrip.id));
  }, [selectedTrip, selectedTripId]);

  useEffect(() => {
    if (routeTripId && trips.some((trip) => String(trip.id) === String(routeTripId))) {
      setSelectedTripId(String(routeTripId));
    }
  }, [routeTripId, trips]);

  if (!session.user) {
    return (
      <section className="content-panel locked">
        <h2>{t('trips.authTitle')}</h2>
        <p>{t('trips.authBody')}</p>
        <button className="primary-button" onClick={() => navigate('/login')} type="button">{t('auth.login')}</button>
      </section>
    );
  }

  async function run(action, successMessage = '') {
    setBusy(true);
    setLocalError('');
    setLocalMessage('');

    try {
      const result = await action();
      if (successMessage) setLocalMessage(successMessage);
      return result;
    } catch (err) {
      setLocalError(err.response?.data?.message || t('messages.apiOffline'));
      return null;
    } finally {
      setBusy(false);
    }
  }

  async function loadPlanner() {
    await run(async () => {
      const [tripResponse, favoriteResponse, ...catalogResponses] = await Promise.all([
        api.get('/trips'),
        api.get('/favorites'),
        ...modules.map((module) => api.get(`/${module.key}`)),
      ]);

      const nextTrips = tripResponse.data.data || [];
      setTrips(nextTrips);
      setFavorites(favoriteResponse.data.data);
      setCatalog(modules.reduce((next, module, index) => ({
        ...next,
        [module.key]: unwrapPage(catalogResponses[index]),
      }), {}));
      if (nextTrips[0] && !selectedTripId) setSelectedTripId(String(nextTrips[0].id));
    });
  }

  async function saveTrip(event) {
    event.preventDefault();
    await run(async () => {
      const response = await api.post('/trips', compactPayload(tripForm));
      const created = response.data.data;
      setTrips((current) => [created, ...current]);
      setSelectedTripId(String(created.id));
      setTripForm(blankTrip);
    }, t('trips.saved'));
  }

  async function deleteTrip() {
    if (!selectedTrip) return;
    if (!window.confirm(t('confirm.deleteTrip', { name: selectedTrip.title }))) return;

    await run(async () => {
      await api.delete(`/trips/${selectedTrip.id}`);
      const nextTrips = trips.filter((trip) => trip.id !== selectedTrip.id);
      setTrips(nextTrips);
      setSelectedTripId(nextTrips[0] ? String(nextTrips[0].id) : '');
    }, t('trips.deleted'));
  }

  async function saveItem(event) {
    event.preventDefault();
    if (!selectedTrip) return;

    const payload = compactPayload({
      ...itemForm,
      item_id: itemForm.item_type === 'custom' ? '' : itemForm.item_id,
    });

    await run(async () => {
      const response = editingItemId
        ? await api.put(`/trips/${selectedTrip.id}/items/${editingItemId}`, payload)
        : await api.post(`/trips/${selectedTrip.id}/items`, payload);
      replaceTrip(response.data.data);
      setItemForm(blankItem);
      setEditingItemId(null);
    }, t('trips.saved'));
  }

  async function deleteItem(item) {
    if (!selectedTrip) return;
    if (!window.confirm(t('confirm.deleteTripItem', { name: item.item?.title || item.custom_title || t('packages.item') }))) return;

    await run(async () => {
      const response = await api.delete(`/trips/${selectedTrip.id}/items/${item.id}`);
      replaceTrip(response.data.data);
    }, t('trips.deleted'));
  }

  function replaceTrip(nextTrip) {
    setTrips((current) => current.map((trip) => (trip.id === nextTrip.id ? nextTrip : trip)));
  }

  function editItem(item) {
    setEditingItemId(item.id);
    setItemForm({
      item_type: item.item_type,
      item_id: item.item_id || '',
      custom_title: item.custom_title || item.item?.title || '',
      custom_description: item.custom_description || item.item?.description || '',
      day_number: item.day_number || 1,
      start_time: item.start_time?.slice(0, 5) || '',
      notes: item.notes || '',
    });
  }

  function selectCatalogItem(value) {
    if (!value) {
      setItemForm((current) => ({ ...current, item_type: 'custom', item_id: '' }));
      return;
    }

    const [itemType, itemId] = value.split(':');
    setItemForm((current) => ({
      ...current,
      item_type: itemType,
      item_id: Number(itemId),
      custom_title: '',
      custom_description: '',
    }));
  }

  return (
    <section className="trip-planner">
      <div className="planner-hero content-panel">
        <div>
          <p className="section-kicker">{t('trips.kicker')}</p>
          <h2>{t('trips.title')}</h2>
          <p>{t('trips.body')}</p>
        </div>
        <div className="planner-meter">
          <strong>{selectedTrip?.items_count ?? selectedTrip?.items?.length ?? 0}</strong>
          <span>{t('trips.itemsLimit')}</span>
        </div>
      </div>

      {(localMessage || localError) && <div aria-live="polite" className={`notice ${localError ? 'error' : ''}`} role={localError ? 'alert' : 'status'}>{localError || localMessage}</div>}

      <div className="planner-grid">
        <aside className="content-panel planner-sidebar">
          <form className="form-grid" onSubmit={saveTrip}>
            <div className="panel-head compact-head">
              <div>
                <p className="section-kicker">{t('trips.create')}</p>
                <h3>{t('trips.newTrip')}</h3>
              </div>
            </div>
            <Field id="trip-title" label={t('trips.tripTitle')}>
              <input id="trip-title" required value={tripForm.title} onChange={(event) => setTripForm((current) => ({ ...current, title: event.target.value }))} />
            </Field>
            <Field id="trip-city" label={t('catalog.city')}>
              <input id="trip-city" value={tripForm.city} onChange={(event) => setTripForm((current) => ({ ...current, city: event.target.value }))} />
            </Field>
            <div className="two-column-fields">
              <Field id="trip-start-date" label={t('trips.startDate')}>
                <input id="trip-start-date" type="date" value={tripForm.start_date} onChange={(event) => setTripForm((current) => ({ ...current, start_date: event.target.value }))} />
              </Field>
              <Field id="trip-end-date" label={t('trips.endDate')}>
                <input id="trip-end-date" type="date" value={tripForm.end_date} onChange={(event) => setTripForm((current) => ({ ...current, end_date: event.target.value }))} />
              </Field>
            </div>
            <Field id="trip-notes" label={t('trips.notes')}>
              <textarea id="trip-notes" value={tripForm.notes} onChange={(event) => setTripForm((current) => ({ ...current, notes: event.target.value }))} />
            </Field>
            <button className="primary-button" disabled={busy || loading} type="submit"><CalendarPlus size={16} />{t('trips.create')}</button>
          </form>

          <div className="trip-selector">
            <label className="field-label" htmlFor="trip-select">{t('trips.selectTrip')}</label>
            <select id="trip-select" value={selectedTripId} onChange={(event) => setSelectedTripId(event.target.value)}>
              {trips.map((trip) => <option key={trip.id} value={trip.id}>{trip.title}</option>)}
            </select>
          </div>
          {!trips.length && <EmptyState text={t('trips.empty')} />}
        </aside>

        <div className="content-panel planner-board">
          <div className="panel-head">
            <div>
              <p className="section-kicker">{selectedTrip?.city || t('catalog.city')}</p>
              <h2>{selectedTrip?.title || t('trips.empty')}</h2>
              {selectedTrip && <p className="planner-dates">{[selectedTrip.start_date, selectedTrip.end_date].filter(Boolean).join(' - ')}</p>}
            </div>
            {selectedTrip && <button className="danger-button" disabled={busy} onClick={deleteTrip} type="button"><Trash2 size={16} />{t('trips.deleteTrip')}</button>}
          </div>

          {selectedTrip ? (
            <>
              <form className="trip-item-form" onSubmit={saveItem}>
                <div className="planner-source-row">
                  <Field id="planner-catalog-source" label={t('trips.addFromCatalog')}>
                    <select id="planner-catalog-source" value={selectedItemValue} onChange={(event) => selectCatalogItem(event.target.value)}>
                      <option value="">{t('trips.customStop')}</option>
                      {catalogOptions.map((option) => <option key={option.value} value={option.value}>{option.label}</option>)}
                    </select>
                  </Field>
                  <Field id="planner-favorite-source" label={t('trips.addFromFavorites')}>
                    <select id="planner-favorite-source" value="" onChange={(event) => selectCatalogItem(event.target.value)}>
                      <option value="">{t('trips.chooseFavorite')}</option>
                      {favoriteOptions.map((option) => <option key={option.value} value={option.value}>{option.label}</option>)}
                    </select>
                  </Field>
                </div>
                {itemForm.item_type === 'custom' && (
                  <div className="planner-source-row">
                    <Field id="planner-custom-title" label={t('packages.customTitle')}>
                      <input id="planner-custom-title" required={itemForm.item_type === 'custom'} value={itemForm.custom_title} onChange={(event) => setItemForm((current) => ({ ...current, custom_title: event.target.value }))} />
                    </Field>
                    <Field id="planner-custom-description" label={t('packages.customDescription')}>
                      <input id="planner-custom-description" value={itemForm.custom_description} onChange={(event) => setItemForm((current) => ({ ...current, custom_description: event.target.value }))} />
                    </Field>
                  </div>
                )}
                <div className="planner-source-row planner-schedule-row">
                  <Field id="planner-day" label={t('packages.day')}>
                    <input id="planner-day" min="1" max="30" type="number" value={itemForm.day_number} onChange={(event) => setItemForm((current) => ({ ...current, day_number: Number(event.target.value) }))} />
                  </Field>
                  <Field id="planner-time" label={t('trips.time')}>
                    <input id="planner-time" type="time" value={itemForm.start_time} onChange={(event) => setItemForm((current) => ({ ...current, start_time: event.target.value }))} />
                  </Field>
                  <Field id="planner-notes" label={t('trips.notes')}>
                    <input id="planner-notes" value={itemForm.notes} onChange={(event) => setItemForm((current) => ({ ...current, notes: event.target.value }))} />
                  </Field>
                  <button className="primary-button" disabled={busy || (selectedTrip.items?.length || 0) >= 30} type="submit">
                    {editingItemId ? <NotebookPen size={16} /> : <Plus size={16} />}
                    {editingItemId ? t('trips.updateItem') : t('trips.addItem')}
                  </button>
                </div>
              </form>

              <div className="itinerary-days">
                {dayGroups.map(([day, items]) => (
                  <div className="itinerary-day" key={day}>
                    <div className="day-marker">{t('packages.day')} {day}</div>
                    <div className="table-list">
                      {items.map((item) => (
                        <div className="list-row itinerary-row" key={item.id}>
                          <div>
                            <strong>{item.item?.title || item.custom_title}</strong>
                            <span>{item.start_time && <><Clock size={14} /> {item.start_time.slice(0, 5)} - </>}{labelForTripItem(item, language)}</span>
                            {item.notes && <span>{item.notes}</span>}
                          </div>
                          <div className="row-actions">
                            <button className="secondary-button" onClick={() => editItem(item)} type="button">{t('trips.editItem')}</button>
                            <button className="danger-button" onClick={() => deleteItem(item)} type="button"><Trash2 size={16} />{t('admin.delete')}</button>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                ))}
                {!selectedTrip.items?.length && <EmptyState text={t('trips.noItems')} />}
              </div>
            </>
          ) : (
            <EmptyState text={t('trips.empty')} />
          )}
        </div>
      </div>
    </section>
  );
}

function compactPayload(payload) {
  return Object.fromEntries(Object.entries(payload).map(([key, value]) => [key, value === '' ? null : value]));
}

function groupItemsByDay(items) {
  return Object.entries(items.reduce((groups, item) => {
    const day = item.day_number || 1;
    groups[day] ||= [];
    groups[day].push(item);
    return groups;
  }, {})).sort(([a], [b]) => Number(a) - Number(b));
}

function buildCatalogOptions(catalog, language, t) {
  return modules.flatMap((module) => (catalog[module.key] || []).map((item) => ({
    value: `${moduleToType[module.key]}:${item.id}`,
    label: `${t(`catalog.${module.key}`)} - ${titleFor(item, module.key, language)}${item.city ? ` (${item.city})` : ''}`,
    description: descriptionFor(item, module.key, language),
  })));
}

function buildFavoriteOptions(favorites, language, t) {
  return Object.entries(favorites).flatMap(([moduleKey, entries]) => (entries || []).map((favorite) => ({
    value: `${favorite.type}:${favorite.item?.id}`,
    label: `${t(`catalog.${moduleKey}`)} - ${titleFor(favorite.item || {}, moduleKey)}${favorite.item?.city ? ` (${favorite.item.city})` : ''}`,
    description: descriptionFor(favorite.item || {}, moduleKey, language),
  })).filter((option) => !option.value.endsWith(':undefined')));
}

function labelForTripItem(item, language) {
  const summary = item.item || {};
  const description = language === 'en' ? summary.description_en : summary.description_fr;
  return description || summary.description || item.custom_description || item.item_type;
}
