#!/bin/bash
#
# This file is part of Budabot.
#
# Budabot is free software: you can redistribute it and/org modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Budabot is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Budabot. If not, see <http://www.gnu.org/licenses/>.
#

case $# in
0)
	php -f mainloop.php ./conf/config.php
;;
1)
	param=`echo $1 | tr '[:upper:]' '[:lower:]'`
	if [ "$param" = "--list" ]
	then
		list=(`ls ./conf/ | grep -oP ".*(?=\\.php)"`)
		for i in ${!list[*]}
		do
			if [ "${list[$i]}" != "config.template" ]
			then
				echo "      ${list[$i]}"
			fi
		done
	else
		if [ "$1" = "config.template" ]
		then
			echo "Error! '$1' is not allowed!"
		else
			php -f mainloop.php ./conf/$param.php
		fi
	fi
;;
*)
	echo "Error! Invalid parameter count!"
	echo "    Either use 'chatbot.sh' for standard"
	echo "    or use 'chatbot.sh <name>' for specific"
;;
esac