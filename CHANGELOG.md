Changelog

## 2.0.0 - 2023-02-06
### Added
- Craft 4.0 support

## 1.3.2 - 2020-01-29
### Changed
- Methods `flipbox\craft\salesforce\fields\Objects::pullFromSalesforce` and `flipbox\craft\salesforce\fields\Objects::pushToSalesforce` are now publicly accessible.

### Changed
- When syncing an object via the field type, checks were added to ensure a Case Safe Object Id is used.

### Fixed
- Error thrown when attempting to create a new element from a sync request.

## 1.3.1 - 2019-9-30
### Changed
- Disabled attachment/document downloads by default.

## 1.3.0 - 2019-9-30
### Added
- Attachment download capabilities

## 1.2.0 - 2019-09-25
### Added
- Document download capabilities
- Webhook processing capabilities (to trigger actions from external sources).

## 1.1.1 - 2019-03-29
### Added
- Admin interactions with the Salesforce API can specific which connection to use

## 1.1.0 - 2019-03-11

### Added
- Connections can be managed via the admin

## 1.0.3 - 2019-02-20
### Added
- Criteria template variables to create criteria used to access date `craft.salesforce.criteria.query`, `craft.salesforce.criteria.object`, `craft.salesforce.criteria.search` 
- `\flipbox\craft\salesforce\records\SOQL::fetch()` to execute the SOQL query API call.

## 1.0.2 - 2019-01-29
### Fixed
- Widget would not create a new element if one was not found.

## 1.0.1 - 2019-01-15
### Changed
- Sync jobs can accept an objectId rather than trying to resolve one.

### Removed
- Checking if field is associated to element prior to syncing as it causes unexpected errors.


## 1.0.0 - 2019-01-10
### Changed
- Supporting new dependencies
- Namespacing

## 1.0.0-rc.3 - 2018-10-09
### Added
- Element Populate transformer can accept a key

## 1.0.0-rc.2 - 2018-10-09
### Added
- Log viewer in admin panel for debugging purposes

## 1.0.0-rc.1 - 2018-09-07
### Added
- Docs link

## 1.0.0-rc - 2018-09-07
### Added
- Connection management via CP

## 1.0.0-beta.13 - 2018-08-01
### Changed
- TransformerHelper inherits the Flux TransformerHelper which assist in resolving a transformer from Yii config

## 1.0.0-beta.12 - 2018-07-18
### Changed
- Moved redundant integration classes into separate package.

## 1.0.0-beta.11 - 2018-07-17
### Fixed
- Issue where objects were not getting associated on new element creation

### Added
- Patron OAuth2 icon

## 1.0.0-beta.10 - 2018-07-16
### Fixed
- Corrected widget class name

## 1.0.0-beta.9 - 2018-07-12
### Changed
- Major refactoring
- 'sobject', 'SObject', 'sObject' references are not 'object' and 'Object'

### Removed
- Salesforce package dependency

## 1.0.0-beta.8 - 2018-05-04
### Changed
- Altered/Removed transformers

## 1.0.0-beta.7 - 2018-05-03
### Changed
- SObject field entries will return more than the limit, even if it exceeds the max

## 1.0.0-beta.6 - 2018-05-03
### Added
- A SObject ID is validated prior to performing an association
- Updated dependencies

## 1.0.0-beta.5 - 2018-04-29
### Changed
- Logging will now occur in a separate force.log file

## 1.0.0-beta.4 - 2018-04-29
### Added
- View Url property to the SObject field which supports direct linking to Salesforce Object
- List Url property to the SObject field which supports direct linking to Salesforce Object List
- The default connection and cache strategy can be set via settings

### Removed
- The following plugins settings: instanceUrl, sObjectViewUrlString, sObjectListUrlString, 

## 1.0.0-beta.3 - 2018-04-27
### Fixed
- SObject fields was returning the first entry for new elements
- SObject field Push Object to Salesforce action only appears on existing entries

## 1.0.0-beta.2 - 2018-04-26
### Fixed
- CP notifications were not being displayed after field actions occurred
- Instance where an environment 
- Sync actions and transformations

## 1.0.0-beta.1 - 2018-04-26
### Fixed
- Class type casing issues
- Issue where a correct cache instance would throw an exception

### Added
- SObject field type supports min and max values

## 1.0.0-beta
Initial release.