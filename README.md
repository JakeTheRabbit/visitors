# Visitor sign-in by Chill Division
A simple facility visitor sign in / out tracker

This is designed to allow visitors to quickly check in to your site, while not causing any issues if they don't sign out.

We also wanted it to be quick and easy to sign out so there are no in-person traffic-jams from that taking place.

![image](https://github.com/user-attachments/assets/00f7afc7-c646-4c2a-b93b-92184f5ee422)

## Install
<pre>git clone https://github.com/Chill-Division/visitors.git</pre>

Edit the details in the schema.sql to add / remove any "Terms" you want the guest to acknowledge. You can add more to the database later and it'll automatically amend what's shown to visitors. There is no "They only accepted some", it's more of a "All the terms were acknowledged" scenario.

Edit the database information in config.php and set a strong admin password

Then import the database:

</pre>mysql -u your_username -p < visitors/schema.sql</pre>

Optionally in process.php there is the capacity to IP-restrict this to just your sites IP address. Useful if it's a publicly accessible sign-in site and you want to avoid any random sign-ins.

Modify your config.php to include the servername / user / pass / dbname too

> **This repo is free. My 2am dryback debugging is not.** If it saved you a crop, a weekend, or a nervous breakdown — [buy the rabbit a bag of nutes](https://github.com/sponsors/JakeTheRabbit). If it didn't, keep your money. I respect a tight nutrient budget.
