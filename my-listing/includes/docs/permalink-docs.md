# Permalink Settings

### How can I modify listing permalinks?
The listing permalink structure can be modified in `WP Admin > Settings > Permalinks`, under the `Listing Base` setting.

The default structure is `https://sitename/listings/listing-name` - however this can be modified to display other data, such as the listing type, region, and category.

The `/listings/` base in the url is not mandatory. A common example is to replace it with the listing type. To achieve that, simply set `Listing Base` to `%listing_type%`.

### What tags can I use?
The currently supported tags are:
- `%listing_type%` - Displays the listing type
- `%listing_category%` - Displays the listing category, or the first selected category in case multiple categories are enabled.
- `%listing_region%` - Displays the listing region, or the first selected region in case multiple regions are enabled.

Multiple tags can be used in the listing base. The below examples are all valid:
- `listings/%listing_type%/`
- `listings/%listing_region%/%listing_category%`
- `%listing_type%/%listing_category%`
- `%listing_region%`
- `explore/%listing_category%`

### How can I modify the value returned by `%listing_type%` tag?
This value can be modified in the listing type editor, which you can access in `WP Admin > Listing Types`. Go to the `General` tab in the editor, and look for the `Permalink` setting.
