# Release Notes

## v1.0.7
### Added
- Feature tests for Controller and Actions
- Example Action for tests
- Create Actions and Channels by Artisan command
### Changed
- Location of database migration
- Example Classes renamed to Test (for Feature Tests)

## v1.0.6
### Fixed
- Parse to int user id in verifyCode()

## v1.0.5
### Fixed
- Namespaces for PSR-4

## v1.0.4
### Added
- Method: `AuthorizationAction::getAuthorizationModel()` to retrieve Authorization from Action

## v1.0.3
### Added
- `AuthorizationAction::sendCode()` to send directly code from controller

## v1.0.2
### Added
- Property `$shouldReturnView` to `AuthorizatorAction` to determinate if returns view or Http code.
### Changed
- `AuthorizationController::create()` uses  `AuthorizatorAction::response()` method instead of `AuthorizatorAction::returnView`.