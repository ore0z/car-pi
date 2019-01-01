#!/usr/bin/env bash
# Setup raspberry pi dashcam and hostspot with RTC

# Check for root access
if [ $UID -ne 0 ]; then
  echo "Please run as root"
  exit 0
fi

# Uninstall option (./install.sh uninstall)
if [[ $1 == "uninstall" ]]; then
  printf "Proceed with uninstall of RTC module? [y/N] "
  read uninstallRTC
  if [[ ${uninstallRTC} == "y" ]]; then
    sed -i '/^dtoverlay=i2c-rtc,ds3231/d' /boot/config.txt
    sed -i '/^\/sbin\/hwclock -s/d' /etc/rc.local
    sed -e '/^HWCLOCKACCESS/ s/^/#/' -i /etc/default/hwclock
    apt install -y fake-hwclock
    echo "RTC uninstalled"
  fi
  printf "Proceed with uninstall of network changes? [y/N] "
  read uninstallNET
  if [[ ${uninstallNET} == "y" ]]; then
    apt purge -y dnsmasq hostapd lighttpd
    rm -rf /etc/hostapd /etc/dnsmasq.conf /etc/default/hostapd /var/www/* /video
    echo "Hostapd, dnsmasq, lighttpd, and /video have been removed"
  fi
exit 0
fi

# Setup RTC (DS3231M)
printf "Do you have a RTC module installed? [y/N] "
read RTCinstalled
if [[ ${RTCinstalled} == "y" ]]; then
  echo "Current time is: $(date)"
  printf "Does the time look correct? [y/N] "
  read dateset
  if [[ ${dateset} != "y" ]]; then
    echo "Please set the date and time and re-run"
    exit 1
  fi
  echo "dtoverlay=i2c-rtc,ds3231" >> /boot/config.txt
  # Remove hwclock imposter since we have a real one
  apt purge -y fake-hwclock
  # Set RTC time
  /sbin/hwclock -w
  # Reconfigure time zone (Just to be safe)
  dpkg-reconfigure tzdata
  # Use RTC on boot
  sed -i '$ i\/sbin/hwclock -s' /etc/rc.local
  # Uncomming the HWCLOCKACCESS line. (value differs on some systems)
  sed -i -e s/^#HWCLOCKACCESS=/HWCLOCKACCESS=/ /etc/default/hwclock
  # Make sure it's set to no to avoid time corruption
  sed -i -e s/^HWCLOCKASSESS=yes/HWCLOCKACCESS=no/ /etc/default/hwclock
  # Double check file
  grep '^HWCLOCKACCESS=no' /etc/default/hwclock || echo  "\nERROR: Check HWCLOCKASSESS=no in /etc/default/hwclock\n"
fi

# Set up video recording
mkdir -p /video

# Set up hostapd for wifi access point
printf "Would you like to setup your pi to broadcast a wifi hotspot (to download video files)? [y/N] "
read wifi
if [[ ${wifi} == "y" ]]; then
  printf "What do you want the SSID (Network name) to be? "
  read ssid
  printf "What will the WPA2 password be for ${ssid} "
  read netpwd
  netdev=$(ifconfig | grep ^wl | awk -F ':' '{print $1}')
  printf "I found wireless device ${netdev} on your system, is that correct [Y/n] "
  read netdevyes
  if [[ ${netdevyes} == "n" ]]; then
    printf "Please enter your wifi device name now: "
    read netdev
  fi
  printf "\nNetwork Setup Summary:\n \
    Device: ${netdev}\n \
    SSID: ${ssid}\n \
    Password: ${netpwd}\n\n"
  printf "Does the above info look correct? [Y/n] "
  read confirm
  if [[ ${confirm} == "n" ]]; then
    echo "Aborting AP setup..."
  else
    apt install -y hostapd dnsmasq
    cat <<EOF >> /etc/dnsmasq.conf
interface=wlan0
dhcp-range=10.0.0.3,10.0.0.20,12h
EOF

    cat <<EOF >> /etc/hostapd/hostapd.conf
interface=${netdev}
driver=nl80211
ssid=${ssid}
hw_mode=g
channel=1
macaddr_acl=0
auth_algs=1
ignore_broadcast_ssid=0
wpa=2
wpa_passphrase=${netpwd}
wpa_key_mgmt=WPA-PSK
rsn_pairwise=CCMP
EOF

    echo DAEMON_CONF="/etc/hostapd/hostapd.conf" >> /etc/default/hostapd
    echo "Done setting up wifi access point."
    echo "Setting up local web server"
    apt install -y lighttpd
    echo 'server.dir-listing = "enable"' >> /etc/lighttpd/lighttpd.conf
    /etc/init.d/lighttpd reload
    rm -rf /var/www/html/*.html # Remove default index.html page
    ln -s /video/ /var/www/html/video # Create symlink to video files
  fi
fi

printf "\nDone with installation choices\n \
You need to reboot changes to take effect\n \
NOTE: If you are connect to your home wifi, you will lose connection after this.\n \
Please use wired connection only from now on.\n\n"
printf "Do you want to restart network services? [Y/n] "
read rld
if [[ ${rld} == "n" ]]; then
  echo "Skipping reloading services, please reboot when you're ready"
else
  service hostapd restart
  service dnsmasq restart
  service lighttpd reload
fi

printf "\n\nSetup complete. Please reboot for full changes to take effect\n\
Connect to your hotspot and visit http://10.0.0.1/ in your browser to download video files\n\
The reboot will take an extra minute or two if you installed a RTC.\n\n"
