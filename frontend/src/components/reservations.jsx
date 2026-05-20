import { Check, Hotel, Utensils, X } from 'lucide-react';
import { useEffect, useState } from 'react';
import { api } from '../lib/api.js';
import { EmptyState } from './common.jsx';

export function MyReservationsView({ t }) {
  const [reservations, setReservations] = useState({ hotels: [], restaurants: [] });
  const [loading, setLoading] = useState(false);
  const [notice, setNotice] = useState('');
  const [error, setError] = useState('');

  async function loadReservations() {
    setLoading(true);
    setError('');
    try {
      const response = await api.get('/my-reservations');
      setReservations(response.data.data);
    } catch (err) {
      setError(err.response?.data?.message || t('messages.apiOffline'));
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadReservations();
  }, []);

  async function cancelReservation(type, id) {
    setLoading(true);
    setNotice('');
    setError('');
    try {
      const endpoint = type === 'hotel' ? `/my-hotel-reservations/${id}/cancel` : `/my-restaurant-reservations/${id}/cancel`;
      await api.put(endpoint);
      setNotice(t('reservations.cancelled'));
      await loadReservations();
    } catch (err) {
      setError(err.response?.data?.message || t('messages.apiOffline'));
    } finally {
      setLoading(false);
    }
  }

  return (
    <section className="stack-view reservations-page">
      {(notice || error) && <div className={`notice ${error ? 'error' : ''}`}>{error || notice}</div>}
      <ReservationGroup
        icon={Hotel}
        items={reservations.hotels || []}
        loading={loading}
        onCancel={(id) => cancelReservation('hotel', id)}
        title={t('reservations.hotels')}
        type="hotel"
        t={t}
      />
      <ReservationGroup
        icon={Utensils}
        items={reservations.restaurants || []}
        loading={loading}
        onCancel={(id) => cancelReservation('restaurant', id)}
        title={t('reservations.restaurants')}
        type="restaurant"
        t={t}
      />
    </section>
  );
}

export function AdminReservationsView({ t }) {
  const [reservations, setReservations] = useState({ hotels: [], restaurants: [] });
  const [loading, setLoading] = useState(false);
  const [statusFilter, setStatusFilter] = useState('');
  const [notice, setNotice] = useState('');
  const [error, setError] = useState('');

  async function loadReservations() {
    setLoading(true);
    setError('');
    try {
      const response = await api.get('/admin/reservations', { params: statusFilter ? { status: statusFilter } : {} });
      setReservations(response.data.data);
    } catch (err) {
      setError(err.response?.data?.message || t('messages.apiOffline'));
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadReservations();
  }, [statusFilter]);

  async function updateStatus(type, id, status) {
    setLoading(true);
    setNotice('');
    setError('');
    try {
      const endpoint = type === 'hotel' ? `/admin/hotel-reservations/${id}/status` : `/admin/restaurant-reservations/${id}/status`;
      await api.put(endpoint, { status });
      setNotice(t('reservations.statusUpdated'));
      await loadReservations();
    } catch (err) {
      setError(err.response?.data?.message || t('messages.apiOffline'));
    } finally {
      setLoading(false);
    }
  }

  return (
    <section className="stack-view reservations-page">
      <div className="content-panel">
        <div className="panel-head">
          <div>
            <p className="section-kicker">{t('admin.dashboard')}</p>
            <h2>{t('reservations.adminTitle')}</h2>
          </div>
          <div className="filters reservation-filter">
            <select value={statusFilter} onChange={(event) => setStatusFilter(event.target.value)}>
              <option value="">{t('reservations.allStatuses')}</option>
              <option value="pending">{t('reservations.status.pending')}</option>
              <option value="confirmed">{t('reservations.status.confirmed')}</option>
              <option value="rejected">{t('reservations.status.rejected')}</option>
              <option value="cancelled">{t('reservations.status.cancelled')}</option>
            </select>
          </div>
        </div>
        {(notice || error) && <div className={`notice ${error ? 'error' : ''}`}>{error || notice}</div>}
      </div>
      <ReservationGroup
        admin
        icon={Hotel}
        items={reservations.hotels || []}
        loading={loading}
        onStatus={(id, status) => updateStatus('hotel', id, status)}
        title={t('reservations.hotels')}
        type="hotel"
        t={t}
      />
      <ReservationGroup
        admin
        icon={Utensils}
        items={reservations.restaurants || []}
        loading={loading}
        onStatus={(id, status) => updateStatus('restaurant', id, status)}
        title={t('reservations.restaurants')}
        type="restaurant"
        t={t}
      />
    </section>
  );
}

function ReservationGroup({ admin = false, icon: Icon, items, loading, onCancel, onStatus, title, type, t }) {
  return (
    <div className="content-panel">
      <div className="panel-head">
        <h2><Icon size={22} /> {title}</h2>
      </div>
      <div className="table-list">
        {items.map((item) => (
          <article className="list-row reservation-row" key={`${type}-${item.id}`}>
            <div>
              <strong>{reservationTitle(item, t)}</strong>
              <span>{reservationDate(item, t)}</span>
              <span>{item.full_name} / {item.email} / {item.phone}</span>
              {item.message && <span>{item.message}</span>}
            </div>
            <div className="reservation-actions">
              <span className={`status-pill ${item.status}`}>{t(`reservations.status.${item.status}`)}</span>
              {!admin && item.status === 'pending' && (
                <button className="danger-button" disabled={loading} onClick={() => onCancel(item.id)} type="button">
                  <X size={16} /> {t('reservations.cancel')}
                </button>
              )}
              {admin && item.status === 'pending' && (
                <>
                  <button className="secondary-button" disabled={loading} onClick={() => onStatus(item.id, 'confirmed')} type="button">
                    <Check size={16} /> {t('reservations.confirm')}
                  </button>
                  <button className="danger-button" disabled={loading} onClick={() => onStatus(item.id, 'rejected')} type="button">
                    <X size={16} /> {t('reservations.reject')}
                  </button>
                </>
              )}
            </div>
          </article>
        ))}
        {!items.length && <EmptyState text={t('catalog.empty')} />}
      </div>
    </div>
  );
}

function reservationTitle(item, t) {
  return item.hotel?.name || item.restaurant?.name || `${t('reservations.request')} #${item.id}`;
}

function reservationDate(item, t) {
  if (item.type === 'hotel') {
    return `${item.check_in_date} -> ${item.check_out_date} / ${item.guests} ${t('reservations.guests').toLowerCase()} / ${item.number_of_rooms} ${t('reservations.rooms').toLowerCase()}`;
  }

  return `${item.reservation_date} ${item.reservation_time} / ${item.guests} ${t('reservations.guests').toLowerCase()}`;
}
