# Custom Taxonomies

### What are taxonomies?
Taxonomies are a way to group listings based on an attribute, e.g. the region, brand, color variation, etc.

There are three default taxonomies: Categories, Regions, and Tags, and it's possible to add new custom ones.

### When should I use a custom taxonomy?
The main difference between custom taxonomies and custom select/multiselect/checkbox/radio fields is **search performance**.

So, if you only plan to display information from a field in the single listing page and preview card,
then a custom select field for example will be enough.

However, if you also need to filter by this information in the Explore page, then using a custom taxonomy provides
significantly better search performance, and every term from this taxonomy gets its unique screen in Explore page.

### Creating a custom taxonomy
To create a custom taxonomy you can go to `WP Admin > Listings > Taxonomies`.

While creating one, you will be asked to add a name and a slug. You can name them whatever you like,
however when adding the slug, make sure it's unique and not used by some other taxonomy or custom field.

Once you create the taxonomy, it will become available in each listing type fields tab under `Available fields`.
You can drag and drop it under your `Used fields` if you want to use this specific taxonomy on that specific listing type.

### Displaying in single listing page and preview card
To display the taxonomy in single listing page, you can go to `Single Page > Content & Tabs` and use a `Terms` block to display it.

To display the taxonomy in the preview card, you can go to the `Preview Card` tab, then under `Footer sections` add a
`Terms` section and choose the taxonomy you created.

### Using as a filter in Explore page
To add it as a filter in your search forms, in the `Search Forms` tab use either a dropdown or checkboxes filter.
