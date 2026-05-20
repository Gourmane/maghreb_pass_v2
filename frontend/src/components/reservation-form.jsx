import { CalendarCheck } from 'lucide-react';
import { useState } from 'react';
import { api } from '../lib/api.js';

export function ReservationForm({ item, moduleKey, session, t }) {
  const isHotel = moduleKey === 'hotels';
  const isRestaurant = moduleKey === 'restaurants';
  const [form, setForm] = useState(() => initialReservationForm(item, isHotel, session));
  const [loading, setLoading] = useState(false);
  const [notice, setNotice] = useState('');
  const [error, setError] = useState('');

  if (!isHotel && !isRestaurant) return null;

  async function submit(event) {
    event.preventDefault();
    setLoading(true);
    setNotice('');
    setError('');

    try {
      const endpoint = isHotel ? '/hotel-reservations' : '/restaurant-reservations';
      await api.post(endpoint, form);
      setNotice(t('reservations.sent'));
      setForm(initialReservationForm(item, isHotel, session));
    } catch (err) {
      setError(err.response?.data?.message || t('messages.apiOffline'));
    } finally {
      setLoading(false);
    }
  }

  return (
    <section className="content-panel reservation-form-panel">
      <div className="panel-head">
        <div>
          <p className="section-kicker">{t('reservations.request')}</p>
          <h2>{isHotel ? t('reservations.hotelRequest') : t('reservations.restaurantRequest')}</h2>
        </div>
      </div>
      {(notice || error) && <div className={`notice ${error ? 'error' : ''}`}>{error || notice}</div>}
      <form className="form-grid" onSubmit={submit}>
        <input type="hidden" value={isHotel ? form.hotel_id : form.restaurant_id} readOnly />
        <ReservationInput label={t('reservations.fullName')} name="full_name" setForm={setForm} value={form.full_name} />
        <ReservationInput label={t('auth.email')} name="email" setForm={setForm} type="email" value={form.email} />
        <ReservationInput label={t('fields.phone')} name="phone" setForm={setForm} value={form.phone} />
        {isHotel ? (
          <>
            <ReservationInput label={t('reservations.checkIn')} name="check_in_date" setForm={setForm} type="date" value={form.check_in_date} />
            <ReservationInput label={t('reservations.checkOut')} name="check_out_date" setForm={setForm} type="date" value={form.check_out_date} />
            <ReservationInput label={t('reservations.rooms')} min="1" name="number_of_rooms" setForm={setForm} type="number" value={form.number_of_rooms} />
          </>
        ) : (
          <>
            <ReservationInput label={t('reservations.date')} name="reservation_date" setForm={setForm} type="date" value={form.reservation_date} />
            <ReservationInput label={t('reservations.time')} name="reservation_time" setForm={setForm} type="time" value={form.reservation_time} />
          </>
        )}
        <ReservationInput label={t('reservations.guests')} min="1" name="guests" setForm={setForm} type="number" value={form.guests} />
        <label className="field reservation-message">
          <span className="field-label">{t('reservations.message')}</span>
          <textarea value={form.message} onChange={(event) => setForm((current) => ({ ...current, message: event.target.value }))} />
        </label>
        <div className="form-actions">
          <button className="primary-button" disabled={loading} type="submit"><CalendarCheck size={16} /> {loading ? t('messages.saving') : t('reservations.send')}</button>
        </div>
      </form>
    </section>
  );
}

function ReservationInput({ label, min, name, setForm, type = 'text', value }) {
  return (
    <label className="field">
      <span className="field-label">{label}</span>
      <input
        min={min}
        onChange={(event) => setForm((current) => ({ ...current, [name]: type === 'number' ? Number(event.target.value) : event.target.value }))}
        required
        type={type}
        value={value}
      />
    </label>
  );
}

function initialReservationForm(item, isHotel, session) {
  const tomorrow = datePlus(1);
  const afterTomorrow = datePlus(2);
  const base = {
    full_name: session.user?.name || '',
    email: session.user?.email || '',
    phone: '',
    guests: 1,
    message: '',
  };

  if (isHotel) {
    return {
      ...base,
      hotel_id: item.id,
      check_in_date: tomorrow,
      check_out_date: afterTomorrow,
      number_of_rooms: 1,
    };
  }

  return {
    ...base,
    restaurant_id: item.id,
    reservation_date: tomorrow,
    reservation_time: '20:00',
  };
}

function datePlus(days) {
  const date = new Date();
  date.setDate(date.getDate() + days);
  return date.toISOString().slice(0, 10);
}
