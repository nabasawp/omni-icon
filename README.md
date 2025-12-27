<p align="center">
  <img src="./omni-icon.svg" alt="Omni Icon Logo" width="100">
</p>

<h1 align="center">Omni Icon</h1>

<p align="center">
  <i>A modern WordPress plugin that seamlessly integrates icons across the WordPress ecosystem with support for multiple page builders, custom icon uploads, and access to 200,000+ icons from Iconify.</i>
</p>

> [!NOTE]
>
> Omni Icon provides enterprise-grade icon management with support for local uploads, bundled icons, and on-demand Iconify API integration.

## Intro

Add beautiful icons to your WordPress site with seamless integration across Gutenberg, Elementor, Bricks, Breakdance, LiveCanvas, or anywhere with the `<omni-icon>` web component.

### Features

- ✅ **Multi-source Icon System**: Upload custom icons, use bundled icons, or access 200,000+ Iconify icons
- 🎨 **Icon Search & Discovery**: Powerful search across all icon sources with intelligent caching
- ⚡️ **Server-Side Rendering (SSR)**: Icons pre-rendered on server for instant display
- 🚀 **Smart Caching**: Multi-layer caching (memory, filesystem, IndexedDB) for optimal performance
- 📦 **Web Component**: Use `<omni-icon>` custom element anywhere in your theme or content
- 🔒 **Secure**: SVG sanitization prevents XSS attacks on uploaded icons
- 🏗️ **Modern Architecture**: Built with PHP 8.2+ attributes, Symfony DI, and auto-discovery

### Integrations

Seamless integration with the most popular visual/page builders:

* [Gutenberg](https://wordpress.org/gutenberg/) / Block Editor — Custom Icon block with live preview
* [Elementor](https://be.elementor.com/visit/?bta=209150&brand=elementor) — Native widget with Elementor controls
* [Bricks](https://bricksbuilder.io/?ref=windpress) — Native element with full theme compatibility
* [Breakdance](https://breakdance.com/ref/165/) — Element Studio integration with SSR
* [LiveCanvas](https://livecanvas.com/?ref=4008) — Custom block with panel controls

## Icon Sources

### Local Icons (Custom Uploads)

> [!WARNING]
> The admin page for uploading local icons is currently in development. However, the REST API endpoints and backend functionality are fully functional.

Upload your own SVG icons via admin page or manually place them in the storage directory:

- Format: `local:icon-name` or `custom-set:icon-name`
- Organized in sets via subdirectories
- SVG sanitization for security
- Manual upload: Place SVG files in the storage directory

**Storage**: `wp-content/uploads/omni-icon/local/`

### Bundle Icons

Pre-packaged icons included with the plugin:

- Prefix: `omni:icon-name`
- Sponsored icons

**Storage**: `/svg` directory in plugin folder

### Iconify Icons

Access to 150+ icon collections with 200,000+ icons:

- Material Design Icons (mdi)
- Font Awesome (fa, fa6-brands, fa6-regular, fa6-solid)
- Bootstrap Icons (bi)
- Hero Icons (heroicons)
- Lucide (lucide)
- And 150+ more collections

Visit [Iconify](https://icon-sets.iconify.design/) to browse available icons.

## Usage

### Web Component

Use the `<omni-icon>` web component directly in your theme or content:

```html
<omni-icon name="mdi:home"></omni-icon>
<omni-icon name="local:my-logo" width="64" height="64"></omni-icon>
<omni-icon name="omni:windpress" color="#3b82f6"></omni-icon>
<omni-icon name="fa6-solid:heart"></omni-icon>
```

**Features**:
- Server-side rendering for instant display
- Lazy loading with smart caching
- Attribute reactivity (changes update in real-time)
- Error handling with visual indicators

### Gutenberg Block

1. In the block editor, add a new "Omni Icon" block
2. Click the icon picker to browse or search icons
3. Select from local, bundle, or Iconify collections
4. Adjust width, height, and color as needed
5. The icon will be rendered on the frontend using SSR

### Page Builders

**Elementor**:
- Add the "Omni Icon" widget from the "Omni Icon" category
- Use native Elementor controls for color, size, and alignment
- Click "Browse Icons" to open the icon picker

**Bricks**:
- Add the "Omni Icon" element from the "general" category
- Full theme compatibility with dynamic data support
- SSR rendering for optimal performance

**Breakdance**:
- Add the "Omni Icon" element from Element Studio
- Custom controls with size and color options
- SSR support via PHP rendering

**LiveCanvas**:
- Add the "Omni Icon" block
- Use custom panel with size slider and color widget
- Client-side state management

## Performance

- ✅ **Lazy Loading**: Web components loaded on-demand
- ✅ **Multi-layer Caching**: Memory → Filesystem → IndexedDB
- ✅ **SSR Support**: Icons pre-rendered on server for instant display
- ✅ **Smart Invalidation**: mtime-based cache invalidation

## Security

- ✅ **SVG Sanitization**: All uploaded SVGs sanitized to prevent XSS (enshrined/svg-sanitize)
- ✅ **MIME Type Validation**: Server-side validation of uploaded files

## Development

Want to contribute or customize the plugin? Check out our [DEVELOPMENT.md](./DEVELOPMENT.md) guide for detailed information about:

- Setting up your development environment
- Understanding the architecture
- Contributing guidelines

## Sponsors

If you like this project, please consider supporting us by becoming a sponsor. Your sponsorship helps us maintain and improve **all our free WordPress plugins**, not just Omni Icon.

### Sponsorship Benefits

As a sponsor, you'll receive benefits across our entire plugin ecosystem:

- 🎨 **Your product/brand icon SVG bundled** in Omni Icon releases (via `omni:your-brand` prefix)
- 📝 **Your logo and link featured** in the README of **all our current and future free plugins**
- ⭐ **Recognition** in the admin area sponsor section across **all our plugins**
- 💼 **Direct exposure** to thousands of WordPress developers and designers using our plugin ecosystem
- 🌟 **Unified sponsor listing** - one sponsorship covers your presence in our entire plugin family

Your icons will be permanently accessible to all Omni Icon users through the `omni:` prefix, and your brand will gain visibility across our growing collection of WordPress tools.

**Supporting one plugin means supporting all our open-source efforts!**

### Become a Sponsor

- [GitHub Sponsors](https://github.com/sponsors/suasgn)
- [Ko-fi](https://ko-fi.com/Q5Q75XSF7)

### Current Sponsors

Thank you to our amazing sponsors who support all our plugin development! 🥰🫰🫶

<!-- Sponsor logos will be displayed here -->

<!-- --- -->

<!-- *Interested in sponsoring? Contact us to discuss custom sponsorship packages tailored to your needs.* -->

## Credits

- Built with [Symfony UX Icons](https://github.com/symfony/ux-icons)
- Powered by [Iconify](https://iconify.design/)
- SVG sanitization by [enshrined/svg-sanitize](https://github.com/darylldoyle/svg-sanitizer)

## Support

For issues, questions, or feature requests, please open an issue on GitHub.
