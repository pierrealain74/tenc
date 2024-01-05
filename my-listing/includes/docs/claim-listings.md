# Claim Listings

This feature can be used to allow the real owner of a business listing posted in your site to take ownership of that listing and be able to edit it. This process can be monetized.

### How to setup claim listings?
1. In `Listings > Settings >` enable `Claim Listings`
2. In `Pages > Add New` create a new page and name it Claim listing
3. In that page, paste the following shortcode in the editor: `[claim_listing]`
  - Important: Do not edit this page using elementor
4. In `Listings > Settings > Claim Listing Page` choose the page you've created
5. Edit the listing type you want to enable claim for, and under `Single Page > Quick Actions` add the `Claim Listing` action.
6. Save changes and the setup should be complete

### How to create claim packages?
Claim packages, like regular listing packages, can be created as WooCommerce Products.
1. In `Products > Add New`, create a new product and set the product type to `Listing Package` or `Listing Package Subscription`
2. After configuring package settings like price, limit, duration, etc. make sure to toggle on `Use for Claims?` setting
3. Edit the listing type you want to use this package for, and under `General > Packages` add the package you created
4. That's it - when a user wants to claim a listing, they can buy this package and submit a new claim entry for review

### How to approve claim entries?
After a new claim entry has been submitted, you can review it in `WP Admin > Listings > Claim Entries`.
Clicking on an entry will allow you to approve or decline it, and send an email to notify the user on the claim status.

On Approval, the claimer will become the author of the requested listing, and the purchased package will be applied to the listing.

If you have enabled `Mark claimed listings as verified` setting in `Listings > Settings > Claim Listings`, then the listing will also become verified at this point.

### Which listings can be claimed?
Once claims are enabled, all listings that don't have a listing package assigned are claimable.

However, in some cases, you may want to assign a package to a listing to give it a better layout and priority, but still keep the listing claimable.
In that case, you can edit the package in `Users > Paid Listing Packages`, and toggle on the `Payment Details > Is claimable?` package setting.

### Where can users see their pending claims?
Users can view all the claim requests they've submitted in `User Dashboard > My Listings > View Claim Requests`.
Additionally, an email confirmation is sent each time a claim status changes.
