# Contributing to Livewire Inline Translation

Thank you for considering contributing to this package! This document provides guidelines for contributing.

## Code of Conduct

- Be respectful and inclusive
- Focus on constructive feedback
- Help others learn and grow

## How to Contribute

### Reporting Bugs

Before creating a bug report:

1. **Check existing issues** - Your bug may already be reported
2. **Test on latest version** - Make sure you're using the latest release
3. **Provide details** - Include steps to reproduce, expected vs actual behavior

**Bug Report Template**:

```markdown
**Description**
A clear description of the bug.

**Steps to Reproduce**
1. Step one
2. Step two
3. ...

**Expected Behavior**
What you expected to happen.

**Actual Behavior**
What actually happened.

**Environment**
- PHP version: 
- Laravel version: 
- Livewire version: 
- Package version: 
```

### Suggesting Features

Feature requests are welcome! Please:

1. **Check existing requests** - Someone may have already suggested it
2. **Explain the use case** - Why is this feature needed?
3. **Provide examples** - Show how it would work

### Pull Requests

#### Before You Start

1. **Open an issue first** - Discuss major changes before implementing
2. **Check existing PRs** - Someone might be working on it already
3. **Follow coding standards** - See below

#### Development Setup

```bash
# Clone the repository
git clone https://github.com/yourusername/livewire-inline-translation.git
cd livewire-inline-translation

# Install dependencies
composer install

# Run tests
composer test
```

#### Making Changes

1. **Create a branch**
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/your-bug-fix
   ```

2. **Make your changes**
   - Write clean, readable code
   - Follow existing code style
   - Add tests for new features
   - Update documentation

3. **Run tests**
   ```bash
   composer test
   ```

4. **Commit your changes**
   ```bash
   git commit -m "Add feature: your feature description"
   ```

5. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Create a Pull Request**
   - Describe what you changed and why
   - Reference any related issues
   - Ensure all tests pass

## Coding Standards

### PHP Code Style

- Follow PSR-12 coding standard
- Use type hints for parameters and return types
- Write descriptive variable and method names
- Add PHPDoc blocks for classes and methods

**Example**:

```php
/**
 * Retrieve a translation from the database.
 *
 * @param string $locale Language code
 * @param string $group File name
 * @param string $key Translation key
 * @return string|null Translation value or null
 */
public static function getTranslation(string $locale, string $group, string $key): ?string
{
    return self::where('locale', $locale)
        ->where('group', $group)
        ->where('key', $key)
        ->value('value');
}
```

### Testing

- Write tests for all new features
- Use Pest for testing
- Aim for high code coverage
- Test both happy paths and edge cases

**Test Example**:

```php
it('can retrieve a translation', function () {
    Translation::create([
        'locale' => 'en',
        'group' => 'website',
        'key' => 'welcome',
        'value' => 'Welcome!',
    ]);

    $value = Translation::getTranslation('en', 'website', 'welcome');

    expect($value)->toBe('Welcome!');
});
```

### Documentation

- Update README.md if you change functionality
- Add/update docs in `/docs` directory
- Use clear, simple English
- Include code examples

### Commit Messages

Use clear, descriptive commit messages:

**Good**:
```
Add support for custom authorization logic
Fix modal not closing after save
Update documentation for configuration options
```

**Bad**:
```
fix bug
update stuff
changes
```

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage

# Run specific test file
vendor/bin/pest tests/Unit/TranslationModelTest.php

# Run specific test
vendor/bin/pest --filter "can retrieve a translation"
```

### Writing Tests

Tests are located in the `tests` directory:

- `tests/Unit/` - Unit tests for individual classes
- `tests/Feature/` - Feature tests for complete functionality

**Test Structure**:

```php
<?php

use ArvidDeJong\LivewireInlineTranslation\Models\Translation;

it('describes what the test does', function () {
    // Arrange - Set up test data
    Translation::create([...]);

    // Act - Perform the action
    $result = Translation::getTranslation(...);

    // Assert - Verify the result
    expect($result)->toBe('expected value');
});
```

## Documentation

### Documentation Structure

```
docs/
├── installation.md      # Installation guide
├── usage.md            # Usage examples
├── configuration.md    # Configuration options
├── how-it-works.md     # Internal architecture
└── api-reference.md    # API documentation
```

### Writing Documentation

- Use clear headings and sections
- Include code examples
- Explain the "why" not just the "what"
- Keep it beginner-friendly

## Release Process

Maintainers will handle releases. The process:

1. Update CHANGELOG.md
2. Update version in composer.json
3. Create a git tag
4. Push to repository

## Questions?

If you have questions:

1. Check the [documentation](docs/)
2. Search [existing issues](https://github.com/yourusername/livewire-inline-translation/issues)
3. Open a new issue with the "question" label

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

## Thank You!

Your contributions make this package better for everyone. Thank you for taking the time to contribute!
