# Security Policy

## Supported Versions

We release patches for security vulnerabilities for the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

## Reporting a Vulnerability

If you discover a security vulnerability within this package, please send an email to Arvid de Jong at **info@arvid.nl**. 

**Please do not create a public GitHub issue for security vulnerabilities.**

All security vulnerabilities will be promptly addressed.

### What to Include

When reporting a vulnerability, please include:

1. **Description** - A clear description of the vulnerability
2. **Steps to Reproduce** - Detailed steps to reproduce the issue
3. **Impact** - What an attacker could potentially do
4. **Suggested Fix** - If you have ideas on how to fix it (optional)

### Response Timeline

- **Initial Response**: Within 48 hours
- **Status Update**: Within 7 days
- **Fix Released**: Depends on severity (critical issues within 7 days)

## Security Best Practices

When using this package:

### 1. Guard Configuration

Always use a secure guard in production:

```php
// config/inline-translation.php
'guard' => 'staff', // Only trusted staff members
```

**Avoid** using the `web` guard in production unless you have additional authorization layers.

### 2. Content Validation

Consider sanitizing HTML input to prevent XSS attacks:

```php
use Illuminate\Support\Str;

public function save(): void
{
    // Sanitize HTML
    $this->translationValue = Str::of($this->translationValue)
        ->stripTags(['strong', 'em', 'a']) // Allow only safe tags
        ->toString();
    
    parent::save();
}
```

### 3. Audit Logging

Track who changes what:

```php
public function save(): void
{
    parent::save();
    
    // Log the change
    logger('Translation updated', [
        'user' => auth()->guard('staff')->user()->email,
        'key' => $this->translationKey,
        'old_value' => $oldValue,
        'new_value' => $this->translationValue,
    ]);
}
```

### 4. Database Security

- Use prepared statements (handled by Eloquent)
- Keep database credentials secure
- Limit database user permissions

### 5. Environment Variables

Never commit sensitive data:

```env
# .env (never commit this file)
INLINE_TRANSLATION_GUARD=staff
DB_PASSWORD=your-secure-password
```

## Known Security Considerations

### XSS Risk

The package uses `{!! !!}` to render HTML, which allows legitimate HTML but also poses an XSS risk if malicious content is entered.

**Mitigation**:
- Only trusted users (staff) can edit by default
- Consider implementing HTML sanitization
- Use Content Security Policy (CSP) headers

### SQL Injection

**Status**: Protected ✅

The package uses Eloquent ORM with parameter binding, which protects against SQL injection.

### CSRF

**Status**: Protected ✅

Livewire handles CSRF protection automatically.

## Updates

Stay informed about security updates:

1. Watch the repository on GitHub
2. Subscribe to release notifications
3. Check CHANGELOG.md regularly

## Credits

We appreciate responsible disclosure of security vulnerabilities. Contributors will be acknowledged (with permission) in the CHANGELOG.
