Moodle 2.x.x Pre-Upgrade Helper
=================================
This repository contains tools for preparing a Moodle **1.9.x** installation for an upgrade to the Moodle **2.x.x** version

What it does?
---------------
The scripts and fixes contained in this repository prepare your moodle database for the **2.x.x** upgrade by automating some of the pre-processes, using optimized tecniques. It follows the "official" procedure, but uses alternative aproaches all for maximum perfomance and time optimization.

What it does not do?
---------------
This tool does not upgrade your moodle installation. It just prepares the database for the upgrade in the fastest way, and applies fixes for some of the known upgrade bugs, that break the upgrade process.

Usage
---------------

1. **Backup** the code, dabatase and data folder, just to be safe.
2. Clone this repository into a location on your drive **large enough** to fit a dump of your moodle database.
3. Configure the **fixes/local_config.php** file.
4. Configure the **scripts/migration_conf** file.
5. Update the **scripts/sql_import** file with your database credentials and location
6. Run the **scripts/prepare** script to have your database prepared for a painless moodle **2.x.x** upgrade

Documentation
---------------
Check out the wiki for documentation of use of the diferent components.

About
---------------
This tool was developed for the upgrade of the moodle installation of the [Rovira i Virgili University](http://moodle.urv.cat).

Tips
---------------
- Before making the "real" upgrade run periodical upgrades on a test machine. This will help catch upgrade bugs before the production upgrade day.
- Run all your commands from a terminal multiplexer like **screen** or **tmux** to avoid the shell becoming unresponsive or having to leave your terminal logged on for the whole process.
- If your installation has **huge** tables of historical logs, these can be upgraded before the rest of the site, during the upgrade tests and later on can be added to the production website. This can short drastically the upgrade time.

License
---------------
GNU GPL v3
Copyright (c) 2013 Robert Boloc

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.