import { ArrowDown, ArrowUp, Plus, Shield, Trash2 } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { api } from '../lib/api.js';
import { Field, ModuleRail } from './common.jsx';
import { adminFieldLabel, emailField, numberOrEmpty, numericField, titleFor, urlField } from '../lib/catalog.js';

export function AdminView({ activeModule, adminForm, catalog, editingId, isAdmin, loading, modules, navigate, onDelete, onEdit, onFormChange, onModuleChange, onReset, onSubmit, stats, t, users }) {
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
          <button className="secondary-button admin-reservation-link" onClick={() => navigate('/admin/reservations')} type="button">
            {t('reservations.adminTitle')}
          </button>
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
                <strong>{user.is_active ? t(`options.role.${user.role}`, { defaultValue: user.role }) : t('common.disabled')}</strong>
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

        {activeModule === 'packages' && (
          <PackageItemManager catalog={catalog} packageId={editingId} t={t} />
        )}

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

        if (field === 'phase') return <SelectField field={field} id={fieldId} key={field} label={adminFieldLabel(field, t)} form={form} onChange={onChange} options={['group', 'round_of_16', 'quarter', 'semi', 'final']} t={t} />;
        if (field === 'status') return <SelectField field={field} id={fieldId} key={field} label={adminFieldLabel(field, t)} form={form} onChange={onChange} options={['upcoming', 'live', 'finished']} t={t} />;
        if (field === 'price_range') return <SelectField field={field} id={fieldId} key={field} label={adminFieldLabel(field, t)} form={form} onChange={onChange} options={['budget', 'moyen', 'gastronomique']} t={t} />;
        if (field === 'is_featured' || field === 'is_active') {
          return (
            <Field id={fieldId} key={field} label={adminFieldLabel(field, t)}>
              <select
                id={fieldId}
                value={String(Boolean(form[field]))}
                onChange={(event) => onChange((current) => ({ ...current, [field]: event.target.value === 'true' }))}
              >
                <option value="false">{t('common.false')}</option>
                <option value="true">{t('common.true')}</option>
              </select>
            </Field>
          );
        }

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

function PackageItemManager({ catalog, packageId, t }) {
  const [packageDetail, setPackageDetail] = useState(null);
  const [form, setForm] = useState({ item_type: 'custom', item_id: '', custom_title: '', custom_description: '', day_number: 1 });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const options = useMemo(() => ({
    hotel: catalog.hotels || [],
    restaurant: catalog.restaurants || [],
    attraction: catalog.attractions || [],
    match: catalog.matches || [],
  }), [catalog]);

  useEffect(() => {
    if (!packageId) {
      setPackageDetail(null);
      return;
    }
    loadPackage();
  }, [packageId]);

  async function loadPackage() {
    setLoading(true);
    setError('');
    try {
      const response = await api.get(`/admin/packages/${packageId}`);
      setPackageDetail(response.data.data);
    } catch (err) {
      setError(err.response?.data?.message || t('messages.apiOffline'));
    } finally {
      setLoading(false);
    }
  }

  async function saveItem(event) {
    event.preventDefault();
    if (!packageId) return;
    setLoading(true);
    setError('');
    try {
      const payload = {
        ...form,
        item_id: form.item_type === 'custom' ? null : Number(form.item_id),
        day_number: Number(form.day_number),
      };
      const response = await api.post(`/admin/packages/${packageId}/items`, payload);
      setPackageDetail(response.data.data);
      setForm({ item_type: 'custom', item_id: '', custom_title: '', custom_description: '', day_number: 1 });
    } catch (err) {
      setError(err.response?.data?.message || t('messages.apiOffline'));
    } finally {
      setLoading(false);
    }
  }

  async function itemAction(item, action) {
    setLoading(true);
    setError('');
    try {
      const endpoint = action === 'delete'
        ? `/admin/packages/${packageId}/items/${item.id}`
        : `/admin/packages/${packageId}/items/${item.id}/move/${action}`;
      const response = action === 'delete' ? await api.delete(endpoint) : await api.put(endpoint);
      setPackageDetail(response.data.data);
    } catch (err) {
      setError(err.response?.data?.message || t('messages.apiOffline'));
    } finally {
      setLoading(false);
    }
  }

  const selectedOptions = options[form.item_type] || [];

  return (
    <div className="content-panel package-admin wide">
      <div className="panel-head">
        <div>
          <p className="section-kicker">{t('packages.items')}</p>
          <h2>{packageDetail?.title || t('packages.selectPackage')}</h2>
        </div>
      </div>
      {error && <div className="notice error">{error}</div>}
      {!packageId && <p className="meta">{t('packages.selectPackageHint')}</p>}
      {packageId && (
        <>
          <form className="form-grid package-item-form" onSubmit={saveItem}>
            <Field id="package-item-type" label={t('packages.itemType')}>
              <select id="package-item-type" value={form.item_type} onChange={(event) => setForm((current) => ({ ...current, item_type: event.target.value, item_id: '' }))}>
                <option value="custom">{t('packages.custom')}</option>
                <option value="hotel">{t('catalog.hotels')}</option>
                <option value="restaurant">{t('catalog.restaurants')}</option>
                <option value="attraction">{t('catalog.attractions')}</option>
                <option value="match">{t('catalog.matches')}</option>
              </select>
            </Field>
            {form.item_type === 'custom' ? (
              <>
                <Field id="package-custom-title" label={t('packages.customTitle')}>
                  <input id="package-custom-title" value={form.custom_title} onChange={(event) => setForm((current) => ({ ...current, custom_title: event.target.value }))} />
                </Field>
                <Field id="package-custom-description" label={t('packages.customDescription')}>
                  <input id="package-custom-description" value={form.custom_description} onChange={(event) => setForm((current) => ({ ...current, custom_description: event.target.value }))} />
                </Field>
              </>
            ) : (
              <Field id="package-item-id" label={t('packages.item')}>
                <select id="package-item-id" value={form.item_id} onChange={(event) => setForm((current) => ({ ...current, item_id: event.target.value }))}>
                  <option value="">{t('packages.selectItem')}</option>
                  {selectedOptions.map((item) => <option key={item.id} value={item.id}>{titleFor(item, moduleKeyForPackageType(form.item_type))}</option>)}
                </select>
              </Field>
            )}
            <Field id="package-day-number" label={t('packages.day')}>
              <input id="package-day-number" min="1" max="30" type="number" value={form.day_number} onChange={(event) => setForm((current) => ({ ...current, day_number: event.target.value }))} />
            </Field>
            <div className="form-actions">
              <button className="primary-button" disabled={loading || (packageDetail?.items?.length ?? 0) >= 30} type="submit"><Plus size={16} />{t('packages.addItem')}</button>
            </div>
          </form>
          <div className="table-list">
            {(packageDetail?.items || []).map((item) => (
              <div className="list-row" key={item.id}>
                <div>
                  <strong>{item.item?.title || item.custom_title}</strong>
                  <span>{t('packages.day')} {item.day_number} - {t(`options.itemType.${item.item_type}`, { defaultValue: item.item_type })}</span>
                </div>
                <div className="row-actions">
                  <button className="secondary-button" disabled={loading} onClick={() => itemAction(item, 'up')} type="button" aria-label={t('packages.moveUp')}><ArrowUp size={16} /></button>
                  <button className="secondary-button" disabled={loading} onClick={() => itemAction(item, 'down')} type="button" aria-label={t('packages.moveDown')}><ArrowDown size={16} /></button>
                  <button className="danger-button" disabled={loading} onClick={() => itemAction(item, 'delete')} type="button"><Trash2 size={16} />{t('admin.delete')}</button>
                </div>
              </div>
            ))}
          </div>
        </>
      )}
    </div>
  );
}

function moduleKeyForPackageType(type) {
  return {
    hotel: 'hotels',
    restaurant: 'restaurants',
    attraction: 'attractions',
    match: 'matches',
  }[type] || type;
}

function SelectField({ field, form, id, label, onChange, options, t }) {
  return (
    <Field id={id} label={label}>
      <select id={id} value={form[field]} onChange={(event) => onChange((current) => ({ ...current, [field]: event.target.value }))}>
        {options.map((option) => <option key={option} value={option}>{t(`options.${field}.${option}`, { defaultValue: option })}</option>)}
      </select>
    </Field>
  );
}
