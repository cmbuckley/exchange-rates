# Changelog

## v2.0.0

Major rewrite of the package to use the new API. Every effort has been made to
maintain backwards compatibilty with v1, but notably API responses are keyed by
currency pairs (e.g. `GBPUSD`) instead of just the destination currency (`USD`).

- Updated all API calls to match the latest exchangerate.host API.
- Relaxed dependency on Carbon to work with DateTimeInterface.
- Changed default URI to HTTP to match the API's free plan.
- Added support for API key and HTTPS option for paid users.

## v1.0.0 (2021-11-21)

- Initial release.
