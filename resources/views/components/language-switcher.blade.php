<div class="language-switcher" dir="ltr">
    <div class="lang-toggle">
        <a href="{{ LaravelLocalization::getLocalizedURL('en') }}"
           class="lang-option {{ LaravelLocalization::getCurrentLocale() == 'en' ? 'active' : '' }}">
            EN
        </a>
        <span class="lang-divider"></span>
        <a href="{{ LaravelLocalization::getLocalizedURL('ar') }}"
           class="lang-option {{ LaravelLocalization::getCurrentLocale() == 'ar' ? 'active' : '' }}">
            AR
        </a>
    </div>
</div>

<style>
.language-switcher {
    position: relative;
}

.lang-toggle {
    display: flex;
    align-items: center;
    background: rgba(124, 58, 237, 0.1);
    border: 1px solid rgba(124, 58, 237, 0.25);
    border-radius: 30px;
    padding: 3px;
    gap: 2px;
}

.lang-option {
    padding: 0.35rem 0.85rem;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.5);
    transition: all 0.25s ease;
    line-height: 1;
}

.lang-option:hover {
    color: rgba(255, 255, 255, 0.85);
}

.lang-option.active {
    background: rgba(124, 58, 237, 0.45);
    border: 1px solid rgba(124, 58, 237, 0.6);
    color: #fff;
    box-shadow: 0 0 12px rgba(124, 58, 237, 0.3);
}

.lang-divider {
    width: 1px;
    height: 14px;
    background: rgba(124, 58, 237, 0.3);
    flex-shrink: 0;
}
</style>