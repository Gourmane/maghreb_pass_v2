import { Hotel, MapPin, Trophy, Utensils } from 'lucide-react';

export const modules = [
  { key: 'matches', icon: Trophy, favoriteType: null, basePath: '/matches' },
  { key: 'hotels', icon: Hotel, favoriteType: 'hotel', basePath: '/hotels' },
  { key: 'restaurants', icon: Utensils, favoriteType: 'restaurant', basePath: '/restaurants' },
  { key: 'attractions', icon: MapPin, favoriteType: 'attraction', basePath: '/attractions' },
];

export const initialForms = {
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
    name: 'Hotel Atlas Rabat',
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
    name: 'Restaurant Medina Rabat',
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
    name: 'Tour Hassan',
    description_fr: 'Site culturel emblematique a decouvrir pendant le sejour.',
    description_en: 'Landmark cultural site to discover during the stay.',
    city: 'Fes',
    address: 'Medina',
    category: 'Musee',
    entry_price: 40,
    opening_hours: '09:00-18:00',
    photos: ['https://upload.wikimedia.org/wikipedia/commons/2/25/Marrakech_Majorelle_Garden_317.JPG'],
  },
};

export function routeFromPath(pathname = window.location.pathname) {
  const clean = pathname.replace(/\/+$/, '') || '/';
  const adminMatch = clean.match(/^\/admin(?:\/(matches|hotels|restaurants|attractions|users))?$/);
  if (adminMatch) return { view: 'admin', module: adminMatch[1] || 'matches' };
  if (clean === '/') return { view: 'home' };
  if (clean === '/matches') return { view: 'public', module: 'matches' };
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

export function titleFor(item, moduleKey) {
  if (moduleKey === 'matches') return `${item.team_home} vs ${item.team_away}`;
  return item.name;
}

export function descriptionFor(item, moduleKey, language) {
  if (moduleKey === 'matches') return `${item.stadium || ''} ${item.group_name ? `- Group ${item.group_name}` : ''}`;
  return language === 'en' ? item.description_en : item.description_fr;
}

export function numericField(field) {
  return ['stars', 'price_min', 'price_max', 'entry_price', 'score_home', 'score_away'].includes(field);
}

export function urlField(field) {
  return field.includes('url') || field === 'photos';
}

export function emailField(field) {
  return field === 'email';
}

export function numberOrEmpty(value) {
  return value === '' ? '' : Number(value);
}

export function adminFieldLabel(field, t) {
  return t(`fields.${field}`, { defaultValue: field.replaceAll('_', ' ') });
}

export function buildParams(moduleKey, filters) {
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
