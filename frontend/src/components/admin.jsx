import { Plus, Shield, Trash2 } from 'lucide-react';
import { Field, ModuleRail } from './common.jsx';
import { adminFieldLabel, emailField, numberOrEmpty, numericField, titleFor, urlField } from '../lib/catalog.js';

export function AdminView({ activeModule, adminForm, catalog, editingId, isAdmin, loading, modules, onDelete, onEdit, onFormChange, onModuleChange, onReset, onSubmit, stats, t, users }) {
  if (!isAdmin) {
    return (
      <section className="content-panel locked">
        <Shield size={40} />
        <h2>{t('admin.lockedTitle')}</h2>
        <p>{t('admin.lockedBody')}</p>
      </section>
    );
  }

  return (
    <section className="workspace">
      <ModuleRail activeModule={activeModule} modules={modules} onModuleChange={onModuleChange} t={t} />
      <div className="admin-grid">
        <div className="content-panel">
          <div className="panel-head"><h2>{t('admin.dashboard')}</h2></div>
          <div className="stats-grid">
            {stats && Object.entries(stats).map(([key, value]) => (
              <div className="stat-tile" key={key}>
                <span>{key}</span>
                <strong>{value}</strong>
              </div>
            ))}
          </div>
          <div className="user-list">
            <h3>{t('admin.users')}</h3>
            {users.map((user) => (
              <div className="compact-row" key={user.id}>
                <span>{user.name}</span>
                <strong>{user.is_active ? user.role : 'disabled'}</strong>
              </div>
            ))}
          </div>
        </div>

        <div className="content-panel">
          <div className="panel-head">
            <h2>{editingId ? t('admin.update') : t('admin.create')} {t(`catalog.${activeModule}`)}</h2>
          </div>
          <AdminForm activeModule={activeModule} form={adminForm} loading={loading} onChange={onFormChange} onReset={onReset} onSubmit={onSubmit} t={t} />
        </div>

        <div className="content-panel wide">
          <div className="panel-head"><h2>{t('admin.content')}</h2></div>
          <div className="table-list">
            {(catalog[activeModule] || []).map((item) => (
              <div className="list-row" key={item.id}>
                <div>
                  <strong>{titleFor(item, activeModule)}</strong>
                  <span>{item.city} {item.status ? `- ${item.status}` : ''}</span>
                </div>
                <div className="row-actions">
                  <button className="secondary-button" disabled={loading} onClick={() => onEdit(item)} type="button">{t('admin.update')}</button>
                  <button className="danger-button" disabled={loading} onClick={() => onDelete(item)} type="button"><Trash2 size={16} />{t('admin.delete')}</button>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

function AdminForm({ activeModule, form, loading, onChange, onReset, onSubmit, t }) {
  return (
    <form className="form-grid" onSubmit={onSubmit}>
      {Object.keys(form).map((field) => {
        const fieldId = `${activeModule}-${field}`;

        if (Array.isArray(form[field])) {
          return (
            <Field hint={t('admin.commaSeparated')} id={fieldId} key={field} label={adminFieldLabel(field, t)}>
              <input
                id={fieldId}
                value={form[field].join(', ')}
                onChange={(event) => onChange((current) => ({ ...current, [field]: event.target.value.split(',').map((value) => value.trim()).filter(Boolean) }))}
                placeholder={adminFieldLabel(field, t)}
              />
            </Field>
          );
        }

        if (field === 'phase') return <SelectField field={field} id={fieldId} key={field} label={adminFieldLabel(field, t)} form={form} onChange={onChange} options={['group', 'round_of_16', 'quarter', 'semi', 'final']} />;
        if (field === 'status') return <SelectField field={field} id={fieldId} key={field} label={adminFieldLabel(field, t)} form={form} onChange={onChange} options={['upcoming', 'live', 'finished']} />;
        if (field === 'price_range') return <SelectField field={field} id={fieldId} key={field} label={adminFieldLabel(field, t)} form={form} onChange={onChange} options={['budget', 'moyen', 'gastronomique']} />;

        return (
          <Field id={fieldId} key={field} label={adminFieldLabel(field, t)}>
            <input
              id={fieldId}
              min={numericField(field) ? '0' : undefined}
              type={field.includes('date') ? 'date' : field.includes('time') ? 'time' : numericField(field) ? 'number' : urlField(field) ? 'url' : emailField(field) ? 'email' : 'text'}
              value={form[field] ?? ''}
              onChange={(event) => onChange((current) => ({ ...current, [field]: numericField(field) ? numberOrEmpty(event.target.value) : event.target.value }))}
              placeholder={adminFieldLabel(field, t)}
            />
          </Field>
        );
      })}
      <div className="form-actions">
        <button aria-busy={loading} className="primary-button" disabled={loading} type="submit"><Plus size={16} /> {loading ? t('messages.saving') : t('admin.save')}</button>
        <button className="secondary-button" disabled={loading} onClick={onReset} type="button">{t('admin.reset')}</button>
      </div>
    </form>
  );
}

function SelectField({ field, form, id, label, onChange, options }) {
  return (
    <Field id={id} label={label}>
      <select id={id} value={form[field]} onChange={(event) => onChange((current) => ({ ...current, [field]: event.target.value }))}>
        {options.map((option) => <option key={option} value={option}>{option}</option>)}
      </select>
    </Field>
  );
}
