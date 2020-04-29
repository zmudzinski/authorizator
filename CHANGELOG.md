# Release Notes

## v1.0.2

### Added
- Property `$shouldReturnView` to `AuthorizatorAction` to determinate if returns view or Http code.

### Changed
- `AuthorizationController::create()` uses  `AuthorizatorAction::response()` method instead of `AuthorizatorAction::returnView`.