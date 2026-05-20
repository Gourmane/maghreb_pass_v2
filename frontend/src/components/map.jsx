import 'leaflet/dist/leaflet.css';
import { useEffect, useMemo, useState } from 'react';
import { CircleMarker, MapContainer, Popup, TileLayer, useMap } from 'react-leaflet';
import { Hotel, Landmark, MapPin, Trophy, Utensils } from 'lucide-react';
import { api } from '../lib/api.js';

const CITY_CENTERS = {
  Casablanca: [33.5899, -7.6039],
  Rabat: [34.0209, -6.8416],
  Marrakech: [31.6295, -7.9811],
  Tanger: [35.7595, -5.8339],
  Agadir: [30.4278, -9.5981],
  Fes: [34.0181, -5.0078],
};

const TYPE_META = {
  all: { color: '#0b6b43', labelKey: 'map.types.all' },
  hotel: { color: '#1f5f91', labelKey: 'map.types.hotel', icon: Hotel },
  restaurant: { color: '#c90d16', labelKey: 'map.types.restaurant', icon: Utensils },
  attraction: { color: '#0b6b43', labelKey: 'map.types.attraction', icon: Landmark },
  match: { color: '#d79b2b', labelKey: 'map.types.match', icon: Trophy },
};

const GROUPS = [
  ['hotels', 'hotel'],
  ['restaurants', 'restaurant'],
  ['attractions', 'attraction'],
  ['matches', 'match'],
];

export function MapView({ navigate, t }) {
  const [city, setCity] = useState('Casablanca');
  const [type, setType] = useState('all');
  const [mapItems, setMapItems] = useState({ hotels: [], restaurants: [], attractions: [], matches: [] });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const markers = useMemo(() => GROUPS.flatMap(([group, itemType]) => (
    mapItems[group] || []
  ).map((item) => ({ ...item, group, type: item.type || itemType }))), [mapItems]);

  useEffect(() => {
    let active = true;
    setLoading(true);
    setError('');

    api.get('/map-items', { params: { city, type } })
      .then((response) => {
        if (!active) return;
        setMapItems({
          hotels: response.data.hotels || [],
          restaurants: response.data.restaurants || [],
          attractions: response.data.attractions || [],
          matches: response.data.matches || [],
        });
      })
      .catch((err) => {
        if (!active) return;
        setError(err.response?.data?.message || t('messages.apiOffline'));
        setMapItems({ hotels: [], restaurants: [], attractions: [], matches: [] });
      })
      .finally(() => {
        if (active) setLoading(false);
      });

    return () => {
      active = false;
    };
  }, [city, type, t]);

  return (
    <section className="map-page">
      <div className="map-hero">
        <div>
          <p className="section-kicker">{t('map.kicker')}</p>
          <h2>{t('map.title')}</h2>
          <p>{t('map.body')}</p>
        </div>
        <div className="map-controls" aria-label={t('map.filters')}>
          <label>
            <span>{t('catalog.city')}</span>
            <select value={city} onChange={(event) => setCity(event.target.value)}>
              {Object.keys(CITY_CENTERS).map((name) => <option key={name} value={name}>{name}</option>)}
            </select>
          </label>
          <label>
            <span>{t('map.type')}</span>
            <select value={type} onChange={(event) => setType(event.target.value)}>
              {Object.keys(TYPE_META).map((key) => <option key={key} value={key}>{t(TYPE_META[key].labelKey)}</option>)}
            </select>
          </label>
        </div>
      </div>

      <div className="map-shell">
        <div className="map-canvas" aria-busy={loading}>
          <MapContainer center={CITY_CENTERS[city]} zoom={13} scrollWheelZoom className="leaflet-map">
            <TileLayer
              attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
              url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            />
            <MapViewport city={city} markers={markers} />
            {markers.map((item) => (
              <CircleMarker
                center={[Number(item.latitude), Number(item.longitude)]}
                key={`${item.type}-${item.id}`}
                pathOptions={{
                  color: TYPE_META[item.type]?.color || TYPE_META.all.color,
                  fillColor: TYPE_META[item.type]?.color || TYPE_META.all.color,
                  fillOpacity: 0.78,
                  weight: 2,
                }}
                radius={9}
              >
                <Popup>
                  <MapPopup item={item} navigate={navigate} t={t} />
                </Popup>
              </CircleMarker>
            ))}
          </MapContainer>
          {loading && <div className="map-loading">{t('messages.loading')}</div>}
        </div>

        <aside className="map-sidebar">
          <div className="map-summary">
            <strong>{markers.length}</strong>
            <span>{t('map.visiblePlaces')}</span>
          </div>
          <div className="map-legend">
            {GROUPS.map(([, itemType]) => {
              const Icon = TYPE_META[itemType].icon || MapPin;
              return (
                <span key={itemType}>
                  <Icon size={16} style={{ color: TYPE_META[itemType].color }} />
                  {t(TYPE_META[itemType].labelKey)}
                </span>
              );
            })}
          </div>
          {error && <div className="notice error" role="alert">{error}</div>}
          {!loading && !markers.length && !error && (
            <div className="empty-state map-empty">
              <MapPin size={24} />
              <span>{t('map.empty')}</span>
            </div>
          )}
          <div className="map-results">
            {markers.map((item) => (
              <button key={`${item.type}-result-${item.id}`} onClick={() => navigate(item.detail_url)} type="button">
                <span className="map-result-dot" style={{ background: TYPE_META[item.type]?.color }} />
                <span>
                  <strong>{item.name}</strong>
                  <small>{t(TYPE_META[item.type]?.labelKey)} / {item.city}</small>
                </span>
              </button>
            ))}
          </div>
        </aside>
      </div>
    </section>
  );
}

function MapViewport({ city, markers }) {
  const map = useMap();

  useEffect(() => {
    if (markers.length > 1) {
      map.fitBounds(markers.map((item) => [Number(item.latitude), Number(item.longitude)]), { padding: [34, 34] });
      return;
    }

    if (markers.length === 1) {
      map.setView([Number(markers[0].latitude), Number(markers[0].longitude)], 14);
      return;
    }

    map.setView(CITY_CENTERS[city], 12);
  }, [city, map, markers]);

  return null;
}

function MapPopup({ item, navigate, t }) {
  return (
    <div className="map-popup">
      {item.image && <img src={item.image} alt="" />}
      <strong>{item.name}</strong>
      <span>{item.city}</span>
      {item.rating && <span>{item.rating}/5</span>}
      <button onClick={() => navigate(item.detail_url)} type="button">{t('catalog.details')}</button>
    </div>
  );
}
