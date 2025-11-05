let preset

try {
    preset = require('./vendor/filament/filament/tailwind.config.preset')
} catch {
    preset = require('./vendor/filament/support/tailwind.config.preset')
}

module.exports = {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
