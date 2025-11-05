import esbuild from 'esbuild'
import fs from 'fs'
import path from 'path'

const isDev = process.argv.includes('--dev')

function getFilamentVersion() {
    try {
        const composerJson = JSON.parse(fs.readFileSync(path.join(process.cwd(), 'vendor', 'filament', 'filament', 'composer.json'), 'utf8'))
        const version = composerJson.version || '3.x'
        return version.split('.')[0] === '4' ? '4.x' : '3.x'
    } catch {
        return '3.x'
    }
}

async function compile(options) {
    const context = await esbuild.context(options)

    if (isDev) {
        await context.watch()
    } else {
        await context.rebuild()
        await context.dispose()
    }
}

const defaultOptions = {
    define: {
        'process.env.NODE_ENV': isDev ? `'development'` : `'production'`,
    },
    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    sourcemap: isDev ? 'inline' : false,
    sourcesContent: isDev,
    treeShaking: true,
    target: ['es2020'],
    minify: !isDev,
    plugins: [{
        name: 'watchPlugin',
        setup: function (build) {
            build.onStart(() => {
                console.log(`Build started at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
            })

            build.onEnd((result) => {
                if (result.errors.length > 0) {
                    console.log(`Build failed at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`, result.errors)
                } else {
                    console.log(`Build finished at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
                }
            })
        }
    }],
}

compile({
    ...defaultOptions,
    entryPoints: ['./resources/js/index.js'],
    outfile: './resources/dist/filament-switch-panel.js',
})
