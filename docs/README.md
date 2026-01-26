# Documentation

Welcome to the Livewire Inline Translation documentation!

## Getting Started

New to the package? Start here:

1. **[Installation Guide](installation.md)** - Get the package up and running
2. **[Usage Guide](usage.md)** - Learn the basics
3. **[Configuration](configuration.md)** - Customize the package

## In-Depth Guides

Once you're familiar with the basics:

- **[How It Works](how-it-works.md)** - Understand the internal architecture
- **[API Reference](api-reference.md)** - Complete API documentation

## Quick Links

### Common Tasks

- [Installing the package](installation.md#step-1-add-package-repository)
- [Basic usage example](usage.md#basic-usage)
- [Changing the authentication guard](configuration.md#authentication-guard)
- [Understanding translation priority](how-it-works.md#translation-retrieval)

### Troubleshooting

- [Modal not showing](installation.md#modal-not-showing)
- [Translations not saving](installation.md#translations-not-saving)
- [Authorization not working](installation.md#authorization-not-working)

## Package Structure

```
livewire-inline-translation/
├── src/                    # Source code
│   ├── Models/            # Translation model
│   ├── InlineTranslation.php
│   └── InlineTranslationServiceProvider.php
├── resources/
│   └── views/             # Blade templates
├── database/
│   └── migrations/        # Database migrations
├── config/                # Configuration files
├── tests/                 # Test suite
├── docs/                  # This documentation
└── README.md              # Main readme
```

## Contributing

Want to contribute? Check out:

- [Contributing Guide](../CONTRIBUTING.md)
- [Security Policy](../SECURITY.md)

## Support

Need help?

1. Check this documentation
2. Search [GitHub Issues](https://github.com/darvis/livewire-inline-translation/issues)
3. Open a new issue

## Version

This documentation is for version **1.0.0** of the package.

## License

This package is open-sourced software licensed under the [MIT license](../LICENSE).
