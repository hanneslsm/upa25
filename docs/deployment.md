# Deployment

This theme deploys via GitHub Actions using FTP Deploy Action.

## Setup

- Add secrets at `https://github.com/USER/REPO/settings/secrets/actions`.
- Create `PROD_FTP_HOST`, `PROD_FTP_PASSWORD`, `PROD_FTP_USER`.
- Create `STAGING_FTP_HOST`, `STAGING_FTP_PASSWORD`, `STAGING_FTP_USER`.
- Set host values as plain server names (e.g. `svr-01.example.cloud`).
- Exclusions are defined in `.github/workflows/production.yml`.

## Behavior

- `staging.yml` deploys on each push to `main`.
- `production.yml` deploys when a GitHub Release is published.

## Release Steps

1. Bump the version in `package.json` and run `npm run build`.
2. Merge to `main` (staging deploys).
3. Draft a release at `https://github.com/USER/REPO/releases` with a `v` tag (e.g. `v0.3.0`).
4. Publishing the release triggers production deploy.
