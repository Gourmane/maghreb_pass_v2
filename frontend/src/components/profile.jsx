import { LogIn, Shield } from 'lucide-react';
import { Field } from './common.jsx';

export function ProfileView({ allowRegister = true, authForm, authMode, loading, onAuthFormChange, onAuthModeChange, onForgotPassword, onProfileChange, onProfileSubmit, onSubmit, profileForm, session, t }) {
  return (
    <section className="profile-grid single-panel">
      <div className="content-panel">
        <div className="panel-head">
          <h2>{session.user ? t('nav.profile') : t(`auth.${authMode}`)}</h2>
        </div>
        {session.user ? (
          <form className="form-grid" onSubmit={onProfileSubmit}>
            <div className="profile-card compact-profile">
              {profileForm.avatar_url ? (
                <img className="profile-avatar" src={profileForm.avatar_url} alt={session.user.name} />
              ) : (
                <Shield size={30} />
              )}
              <h3>{session.user.email}</h3>
              <span>{session.user.role}</span>
            </div>
            <Field id="profile-name" label={t('auth.name')}>
              <input autoComplete="name" id="profile-name" maxLength="120" value={profileForm.name} onChange={(event) => onProfileChange((current) => ({ ...current, name: event.target.value }))} placeholder={t('auth.name')} />
            </Field>
            <Field id="profile-avatar" label={t('auth.avatarUrl')}>
              <input autoComplete="url" id="profile-avatar" type="url" value={profileForm.avatar_url} onChange={(event) => onProfileChange((current) => ({ ...current, avatar_url: event.target.value }))} placeholder={t('auth.avatarUrl')} />
            </Field>
            <Field id="profile-language" label={t('auth.language')}>
              <select id="profile-language" value={profileForm.preferred_language} onChange={(event) => onProfileChange((current) => ({ ...current, preferred_language: event.target.value }))}>
                <option value="fr">FR</option>
                <option value="en">EN</option>
              </select>
            </Field>
            <button aria-busy={loading} className="primary-button" disabled={loading} type="submit">{loading ? t('messages.saving') : t('admin.save')}</button>
          </form>
        ) : (
          <form className="form-grid" onSubmit={onSubmit}>
            {allowRegister && (
              <div className="segmented">
                <button aria-pressed={authMode === 'login'} className={authMode === 'login' ? 'active' : ''} onClick={() => onAuthModeChange('login')} type="button">{t('auth.login')}</button>
                <button aria-pressed={authMode === 'register'} className={authMode === 'register' ? 'active' : ''} onClick={() => onAuthModeChange('register')} type="button">{t('auth.register')}</button>
              </div>
            )}
            {authMode === 'register' && (
              <Field id="auth-name" label={t('auth.name')}>
                <input autoComplete="name" id="auth-name" maxLength="120" required value={authForm.name} onChange={(event) => onAuthFormChange((current) => ({ ...current, name: event.target.value }))} placeholder={t('auth.name')} />
              </Field>
            )}
            <Field id="auth-email" label={t('auth.email')}>
              <input autoComplete="email" id="auth-email" required type="email" value={authForm.email} onChange={(event) => onAuthFormChange((current) => ({ ...current, email: event.target.value }))} placeholder={t('auth.email')} />
            </Field>
            <Field id="auth-password" label={t('auth.password')}>
              <input autoComplete={authMode === 'login' ? 'current-password' : 'new-password'} id="auth-password" minLength="8" required type="password" value={authForm.password} onChange={(event) => onAuthFormChange((current) => ({ ...current, password: event.target.value, password_confirmation: event.target.value }))} placeholder={t('auth.password')} />
            </Field>
            {authMode === 'register' && (
              <Field id="auth-language" label={t('auth.language')}>
                <select id="auth-language" value={authForm.preferred_language} onChange={(event) => onAuthFormChange((current) => ({ ...current, preferred_language: event.target.value }))}>
                  <option value="fr">FR</option>
                  <option value="en">EN</option>
                </select>
              </Field>
            )}
            <button aria-busy={loading} className="primary-button" disabled={loading} type="submit"><LogIn size={17} /> {loading ? t('messages.loading') : t(`auth.${authMode}`)}</button>
            {authMode === 'login' && <button className="secondary-button" disabled={loading} onClick={onForgotPassword} type="button">{t('auth.forgotPassword')}</button>}
          </form>
        )}
      </div>
    </section>
  );
}
