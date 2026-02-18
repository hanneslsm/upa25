# Agents

## Automation Standards

- Follow-up tasks (file renames, reference updates, consistency checks) must be completed automatically in the same operation.
- Never introduce typos intentionally or leave them uncorrected.
- When making changes across multiple files, verify consistency and make all related updates together.
- Always double-check for side effects: document references, titles, and cross-theme consistency.

- Follow WordPress coding standards for PHP, JavaScript, and CSS.
- Prioritize security: sanitize input, escape output, verify nonces, and check capabilities.
- Deliver accessible experiences: keyboard navigation, focus visibility, contrast, and reduced motion.
- Maintain performance: minimize queries, cache where appropriate, and load only required assets.
- Keep code clean, readable, and maintainable with minimal complexity.

## Documentation and Comments

- Keep README.md, readme.txt, and any guide docs current and consistent.
- Review docs in `docs/` (including `docs/src.md`) when working in `src/`.
- Use WordPress-style docblocks and short, meaningful comments that explain intent.
- Always keep documentation up to date with code and workflow changes.

## Development Workflow

- Work in feature branches with clear names and small, focused commits.
- Run the common tasks during development:
  - `npm run lint:css`
  - `npm run lint:css:fix`
  - `npm run lint:js`
  - `npm run format`
  - `npm run format:reorder`
- Run `npm run build` before committing and commit compiled assets when required.
- Test in both the block editor and front end before opening a PR.

## General Expectations

- Prefer WordPress APIs and Gutenberg conventions over custom reimplementation.
- Avoid unnecessary abstractions; keep changes straightforward and reliable.
- Validate changes against the intended design and content requirements.

## Checks

- Smoke test key templates in editor and frontend.
- Confirm focus order, contrast, reduced motion, and skip-links.
- Rebuild assets after changes and re-verify output.
- Keep version bumps aligned with release prep.
