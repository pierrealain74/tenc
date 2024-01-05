# Paid Listings
This feature allows you to create paid listing plans using WooCommerce.

### How to setup paid listings?
1. First, toggle on the `Enable Paid Listings` setting in `WP Admin > Listings > Settings`
2. You must now setup the different listing plans. To do this:
  1. Create a new product in `WP Admin > Products > Add New`
  2. Set the Product Type to `Listing Package` or `Listing Subscription` (more on subscriptions later)
  3. Configure the different package settings, like the listing limit, duration, priority, verified status, etc.
  4. Publish the product. Do this process for every listing plan you want to have, e.g. Basic, Advanced, and Premium packages.
3. Edit the listing type you want to enable paid listing for, and in `General > Packages`, insert the listing plans you want to use.  
You can have different plans for different listing types, and even disable paid listings for a listing type, but enable it for others.
4. *That's it*. If you now go to Add Listing page and try to create a new listing, you'll be presented with the Pricing Plans page.

### How do paid listing submissions work?
1. After the user has selected a plan, filled the listing details, and previewed the listing, they'll be redirected to the WooCommerce checkout page.
2. After they've filled the checkout details and submitted, a new WooCommerce Order is created.
3. In `WP Admin > WooCommerce > Orders`, you can verify the order and mark it as complete.
4. Once the payment is done and verified, a new paid listing package will be created for that user. You can view the package details in `WP Admin > Users > Paid Listing Packages`.
5. This paid listing package will also be immediately assigned to the submitted listing, and the listing will either get published or sent for review,
depending on the `Require admin approval of all new listing submissions` setting in `WP Admin > Listings > Settings`.

### Submitting listings using an already owned package
If you've set the listing limit for a package to more than 1, then the user that owns this package can add other listings until the limit is reached.

In the Pricing Plans step, if the user already owns a package, they can click on `Use Available Package` and immediately submit the listing,
without having to go to Checkout page.

### Submitting listings using a free package
In addition to premium plans, you can also have a free listing plan. By setting the product price to zero, listings submitted with this plan will skip checkout
as that's not required in this case. You can also toggle on the `Disable repeat purchase?` setting to only allow the user to use this plan once.

### What are the differences between different listing plans?
Other than the package settings like duration, limit, priority, etc. you can also have different sets of listing fields and different content in single listing
page based on the listing plan.

This is made possible by the `Enable Package Visibility` setting for each listing field, which you can find by editing the listing type, going to `Fields` tab and clicking on
a field to expand details.

This way, you can have some basic fields for a cheap or free package, but then show an extended form for premium plans.

### Subscription based listing plans
Subscription based plans are possible through the WooCommerce Subscriptions plugin. Once installed and activated, you can create a new
product of type `Listing Subscription` in `WP Admin > Products > Add New`.

While the product settings are similar to regular listing plans, there's a `Subscription Type` setting, which works as follows:

### Subscription Type: *Link the subscription to posted listings (renew posted listings every subscription term)*
This type of subscriptions ties the expiration date of listings to that of the subscription itself.

In other words, as long as the subscription is renewed by the user, listings will remain active. Once the subscription ends or is cancelled, listings will expire.

If the subscription is re-activated after it has expired, the listings will go back to being published again.

Free trials are supported as well. At the end of the trial period, the listings will be set to Expired, until the subscription is renewed.

### Subscription Type: *Link the subscription to the package (renew listing limit every subscription term)*
With this type of subscriptions, the generated paid listing package will have it's listing count reset to zero on every renewal.

For example, if the subscription is renewed every month, and the package limit is 10, the user will be able to submit 10 listings every month using this package.

The expiration date of these listings won't be related to the subscription itself, that's a separate setting which defines how long listings stay published for.

Once the subscription ends or is cancelled, listings will remain published until their expiration date is reached. However, once the package limit is reached, the
user won't be able to create any new additional listings anymore, unless they re-activate the subscription.
