---
title: Doctrine 1.2.1 Released
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: [release]
permalink: /:year/:month/:day/:basename.html
---
Today we are happy to bring you the first maintenance release for the
Doctrine 1.2 version. We will continue to have regular maintenance
releases for the 1.2 branch for the next 18 months. On average we will
have one release per month containing bug fixes only. These releases are
meant to be fully backwards compatible so it is recommended that you
upgrade right away!

Below are some of the changes made in this release:

<ul>
  <li>

[r6834] Fixing issue with relationship ordering

</li>
  <li>

[r6835] Fixes issue with oracle adapter statement using wrong constant

</li>
  <li>

[r6836] Fixing issue with sfYaml autoload not returning true

</li>
  <li>

[r6839] Fixes issue with array cache driver and deleting

</li>
  <li>

[r6840] Fixed thrown Exceptions to be package-level

</li>
  <li>

[r6842] Fixes issue with \$length in migrations addColumn

</li>
  <li>

[r6859] Fixed misplaced param when parsing join condition

</li>
  <li>

[r6883] Added empty init() method implementation to avoid method does
not exist error

</li>
  <li>

[r6889] Fixing issue with nested set createRoot() method and string root
column

</li>
  <li>

[r6893] Adding \_cleanup() call to start of migrations diff to make sure
directory is clean

</li>
</ul>

You can view the full [change
log](http://www.doctrine-project.org/change_log/1_2_1) and
[download](http://www.doctrine-project.org/download#1_2) now! If you
encounter any issues please report them in
[Jira](http://www.doctrine-project.org/jira).
