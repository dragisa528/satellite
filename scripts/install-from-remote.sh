#!/usr/bin/env bash

# This is a use-once script to get a website running using a remote database,
# however the usual sync script can't be run until WordPress can be
# bootstrapped, so this just does a quick clean install before running that
# command.

if wp core is-installed; then
	echo "WordPress is already installed. Use the regular 'wp satellite sync' command."
	exit
fi

if [ ! -f "./vendor/bin/wp" ]; then
	echo "Cannot find './vendor/bin/wp'. Please run this from the project root."
fi

# This is just so we can run `wp satellite sync` so the weak credentials are
# never actually used.
wp core install \
	--url=example.com \
	--title=Example \
	--admin_user=admin \
	--admin_password=weakpassword \
	--admin_email=admin@example.com

# Invoke our regular database sync
./vendor/bin/wp satellite sync --database
