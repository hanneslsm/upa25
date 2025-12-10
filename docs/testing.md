<!---
 * @package upa25
 * @version 0.1.0
-->

# Testing Documentation

## Overview

The Swedenlecture25 theme includes a comprehensive test suite that validates code quality, security, accessibility, and WordPress coding standards. The tests are automated bash scripts that run static analysis on your theme files.

## Quick Start

Run all tests:
```bash
npm test
```

Or run individual test suites:
```bash
npm run test:patterns      # Block pattern validation
npm run test:security      # Security checks
npm run test:accessibility # Accessibility validation
npm run test:standards     # WordPress coding standards
```

## Test Suites

### 1. Pattern Validation (`test-patterns.sh`)

Validates that all WordPress block patterns meet theme requirements.

**Tests Include:**
- ✅ Required pattern headers (Title, Slug, Categories)
- ✅ Correct theme namespace (`swedenlecture25/`)
- ✅ Package documentation (`@package ProLooks`)
- ✅ Valid PHP syntax
- ℹ️ Pattern metadata presence (optional)
- ℹ️ Translation function usage

**Example Output:**
```
Testing 56 pattern files (excluding standard templates)...
✅ All patterns have required headers
✅ All patterns use correct namespace (swedenlecture25)
```

### 2. Security Validation (`test-security.sh`)

Checks for common security vulnerabilities in WordPress theme files.

**Tests Include:**
- ✅ Proper output escaping (`esc_html`, `esc_attr`, `esc_url`)
- ✅ Dangerous functions detection (`eval`, `exec`, `shell_exec`)
- ✅ Hardcoded internal asset URLs
- ✅ Hardcoded credentials
- ✅ Direct database queries (SQL injection risks)
- ✅ File upload security
- ✅ Form nonce protection (CSRF prevention)
- ⚠️ Superglobal sanitization (`$_GET`, `$_POST`, `$_SERVER`)

**Common Warnings:**
- **Superglobal Usage**: The test flags any `$_GET`, `$_POST`, `$_SERVER` usage that doesn't include explicit sanitization functions. Review these manually to ensure they use `sanitize_text_field()`, `absint()`, or similar WordPress sanitization functions.

### 3. Accessibility Validation (`test-accessibility.sh`)

Ensures the theme follows WCAG accessibility guidelines.

**Tests Include:**
- ⚠️ Image alt attributes (detects missing `alt` tags)
- ✅ Heading hierarchy (H1 usage, skipped levels)
- ✅ ARIA labels and landmarks
- ✅ Form input labels
- ✅ Link accessibility (generic text like "click here")
- ✅ Language attributes
- ✅ Color contrast (basic check)

**Common Warnings:**
- **Placeholder Images**: Decorative placeholder images in patterns should have `alt=""` or `role="presentation"` to avoid false positives.
- **Heading Levels**: The test warns if heading levels skip (e.g., H1 → H3). Ensure proper semantic structure.

**Note:** This is a static analysis tool. For comprehensive accessibility testing, use browser tools like [axe DevTools](https://www.deque.com/axe/devtools/).

### 4. WordPress Standards Validation (`test-standards.sh`)

Validates compliance with WordPress coding standards and best practices.

**Tests Include:**
- ✅ PHP syntax validation
- ⚠️ Function naming conventions (theme prefix required)
- ✅ Text domain consistency (`swedenlecture25`)
- ✅ Proper WordPress escaping functions
- ✅ WordPress API usage (prefer `get_template_part()`)
- ✅ Asset enqueuing (use `wp_enqueue_script/style()`)
- ⚠️ WordPress hooks usage (`add_action`, `add_filter`)
- ✅ Deprecated function detection
- ✅ Code formatting (indentation consistency)
- ✅ Theme structure (required files present)
- ℹ️ PHPCS installation check (optional)

**Common Warnings:**
- **Function Naming**: All custom functions should be prefixed with `swedenlecture25_` or `prolooks_` to avoid conflicts with plugins or other themes.

## Understanding Test Results

### Status Indicators

- ✅ **Pass**: Test completed successfully with no issues
- ❌ **Fail**: Critical issue found, must be fixed
- ⚠️ **Warning**: Potential issue detected, review recommended
- ℹ️ **Info**: Informational output, no action required

### Exit Codes

- `0`: All tests passed
- `1`: One or more tests failed (blocks CI/CD)

### Warning Count

The master test script counts all warnings across all test suites and displays them in the summary:

```
╔════════════════════════════════════════════════════════════╗
║                      TEST SUMMARY                          ║
╠════════════════════════════════════════════════════════════╣
║  Total Tests Run:    4                                    ║
║  Passed:             4                                    ║
║  Failed:             0                                    ║
║  Warnings:           5                                    ║
╚════════════════════════════════════════════════════════════╝
```

## Continuous Integration

### GitHub Actions Integration (Optional)

The test suite is **not currently integrated** into your GitHub Actions workflows. Your workflows only build and deploy the theme.

To add automated testing to your CI/CD pipeline, you can add a test step before deployment:

```yaml
- name: 🧪 Run Tests
  run: npm test
```

This step should be added after `npm install` and before the FTP sync in both `staging.yml` and `production.yml`. The workflow will fail if any test returns a non-zero exit code, preventing deployment of broken code.

## Local Development

### Running Tests Before Commit

It's recommended to run tests before committing:

```bash
npm test
```

## Customizing Tests

### Modifying Test Scripts

All test scripts are located in `scripts/`:
- `test-all.sh` - Master orchestrator
- `test-patterns.sh` - Pattern validation
- `test-security.sh` - Security checks
- `test-accessibility.sh` - Accessibility validation
- `test-standards.sh` - WordPress standards

### Adding New Tests

To add a new test suite:

1. Create a new script in `scripts/`:
   ```bash
   touch scripts/test-performance.sh
   chmod +x scripts/test-performance.sh
   ```

2. Add the test logic following the existing pattern:
   ```bash
   #!/bin/bash
   FAILED=0
   echo "=== PERFORMANCE VALIDATION ==="
   # Your tests here
   exit $FAILED
   ```

3. Add it to `test-all.sh`:
   ```bash
   run_test "test-performance.sh" "Performance Validation"
   ```

4. Add npm script to `package.json`:
   ```json
   "test:performance": "bash scripts/test-performance.sh"
   ```

### Excluding Files from Tests

Most tests already exclude:
- `vendor/*` (Composer dependencies)
- `node_modules/*` (npm packages)
- Standard WordPress template files (in pattern tests)

To exclude additional paths, modify the `find` commands in individual test scripts.


## Support

For issues or questions about the test suite, please open an issue on the [GitHub repository](https://github.com/hanneslsm/swedenlecture25).
