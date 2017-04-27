<div class="text">S<span class="red">y</span>DES Installer</div>

<div class="group">Hello, Bonjour, Привет!</div>
<div class="group"><?=H::select('locale', '', $locales, ['class' => ['input']]);?></div>

<script>
    var lang = (navigator.languages && navigator.languages.length) ?
            navigator.languages[0] : navigator.language || navigator.userLanguage,
        fallback = lang.split('-')[0];
    var select = document.getElementsByName('locale')[0], locales = [];
    for (var i in select.options) {
        if (!select.options.hasOwnProperty(i)) continue;
        locales.push(select.options[i].value);
    }
    if (locales.indexOf(lang) != -1) {
        select.value = lang;
    } else if (locales.indexOf(fallback) != -1) {
        select.value = fallback;
    } else {
        select.value = 'en';
    }
</script>
