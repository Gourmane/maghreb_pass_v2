import { CalendarDays, Check, Trophy } from 'lucide-react';
import React from 'react';
import { titleFor } from '../lib/catalog.js';

export function ModuleRail({ activeModule, modules, onModuleChange, t }) {
  return (
    <aside className="module-rail">
      {modules.map(({ key, icon: Icon }) => (
        <button aria-pressed={activeModule === key} className={activeModule === key ? 'active' : ''} key={key} onClick={() => onModuleChange(key)} type="button">
          <span className="module-icon"><Icon size={20} /></span>
          <span>{t(`catalog.${key}`)}</span>
        </button>
      ))}
    </aside>
  );
}

export function Media({ item, moduleKey }) {
  const image = item.photos?.[0];
  if (image) return <img className="media" src={image} alt={titleFor(item, moduleKey)} decoding="async" loading="lazy" />;
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
      {src ? <img src={src} alt={`${name} flag`} decoding="async" loading="lazy" /> : <Trophy size={28} />}
      <strong>{code || name}</strong>
    </div>
  );
}

export function CardFacts({ item, moduleKey, t }) {
  if (moduleKey === 'matches') return <div className="facts"><span>{item.match_date}</span><span>{item.match_time}</span><span>{t(`status.${item.status}`)}</span></div>;
  if (moduleKey === 'hotels') return <div className="facts"><span>{item.stars} stars</span><span>{item.price_min}-{item.price_max} {item.currency}</span></div>;
  if (moduleKey === 'restaurants') return <div className="facts"><span>{item.cuisine_type}</span><span>{item.price_range}</span></div>;
  return <div className="facts"><span>{item.category}</span><span>{item.entry_price ?? 0} MAD</span></div>;
}

export function EmptyState({ text }) {
  return <div className="empty-state"><Check size={24} /><span>{text}</span></div>;
}

export function Field({ children, compact = false, hint = '', id, label }) {
  const hintId = hint ? `${id}-hint` : undefined;
  const child = React.Children.only(children);

  return (
    <div className={`field ${compact ? 'compact-field' : ''}`}>
      <label className={compact ? 'sr-only' : 'field-label'} htmlFor={id}>{label}</label>
      {React.cloneElement(child, {
        'aria-describedby': hintId,
      })}
      {hint && <small id={hintId}>{hint}</small>}
    </div>
  );
}
