# Bracket Syntax

You can output a listing field's value by wrapping the field name in double brackets. For example, to get the listing description, use this: `[[description]]`

This will also work for custom fields. If you have a field named "Price per day", then you can get it's value using `[[price-per-day]]`

Type `@` or `[[` in the form input to see the list of all available fields.

---

In addition to custom fields, you can also output other listing data, through the following keys:

| Key                           | Outputs                                                        |
| ----------------------------- | -------------------------------------------------------------- |
| `[[:id]]`                     | Listing ID                                                     |
| `[[:url]]`                    | Listing URL                                                    |
| `[[:authid]]`                 | Author user ID                                                 |
| `[[:authname]]`               | Author display name                                            |
| `[[:authlogin]]`              | Author username                                                |
| `[[:lat]]`                    | Latitude                                                       |
| `[[:lng]]`                    | Longitude                                                      |
| `[[:reviews-average]`]        | Average review score                                           |
| `[[:reviews-count]`]          | Number of reviews                                              |
| `[[:reviews-mode]`]           | Review mode: 5 or 10 stars                                     |
| `[[:reviews-stars]`]          | Average rating represented with stars                          |
| `[[:currentuserid]]`          | Logged in user ID                                              |
| `[[:currentusername]]`        | Logged in user display name                                    |
| `[[:currentuserlogin]]`       | Logged in user username                                        |
| `[[:date]]`                   | Date listing was created at, formatted to site's date settings |
| `[[:rawdate]]`                | Date listing was created at, unformatted                       |
| `[[:last-modified]]`          | Date listing was last edited/modified                          |