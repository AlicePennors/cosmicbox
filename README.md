# CosmicBox

* v0.1 (Mathieu Soula - msoula@gmx.com)
* based on LibraryBox v2.0 (Jason Griffey - griffey@gmail.com)
* based on PirateBox (David Darts - daviddarts@gmail.com)

## INSTALLING FROM GIT

The CosmicBox project has a buildroot submodule. You should enter the
following command to clone this repo:

    git clone --recursive https://github.com/msoula/cosmicbox.git

## WHAT IS IT?

A PirateBox is a portable electronic device, often consisting in a combination of a Wi-Fi router
and a device for storing information. It creates a wireless network that allows
users who are connected to share files anonymously and locally. Conceived by David Darts, the PirateBox
is designed to freely exchange data in the public domain or under a free license.
More information : http://piratebox.cc/start

Built by Jason Griffey, LibraryBox is a fork of PirateBox for TP-Link MR 3020. LibraryBox is a digital
distribution tool for education, libraries, healthcare, and emergency response.
More information: http://www.librarybox.us

The CosmicBox project aims to provide a fully functionnal LibraryBox for
RaspberryPi. This project was started by Les Chats Cosmiques, a French
non-profit organization.
More information: http://cosmicbox.leschatscosmiques.net

## WHAT DOES IT DO?

Basically, a CosmicBox is a LibraryBox for RaspberryPi.

This project aims to provide a custom RaspberryPi Linux distribution designed
to embed a LibraryBox. The generated system was carefully designed to be secure
and fully customizable. Apart from that, the CosmicBox has the same features
as a standard LibraryBox:
 - file sharing;
 - chat;
 - usage statistics.
But it also allows users to add new features designed by Mathieu Soula for Les Chats Cosmiques and their partners.

## ARCHITECTURE OF THE COSMICBOX

A CosmicBox is designed to work with two partitions.

The first one contains the kernel linked with standard file system. Both
components are generated by buildroot (2015.02).

The second one contains the LibraryBox file system.

    /cosmicbox
    +-- bin/                      [0]
    +-- cosmicbox                 [1]
    +-- cosmicbox.conf            [2]
    +-- etc/                      [3]
    +-- etc.templates/            [4]
    +-- lib/                      [5]
    `-- www/                      [6]

[0] directory containing some LibraryBox applications (shoutbox, ...).
[1] LibraryBox start/stop script (launched by init).
[2] LibraryBox main configuration file.
[3] directory that contain LibraryBox configuration files once they have
    been completed.
[4] directory containing templates of LibraryBox configuration files. At boot,
    these templates are completed then copied into /cosmicbox/etc directory.
[5] directory containing LibraryBox mandatory libraries.
[6] directory containing the first part of the LibraryBox website (the other
    part is located on the storage device).

At boot time, once every daemons have started, the script /cosmicbox/cosmicbox
is run by the init process.
All the templates of the configuration file are completed from data extracted from
/cosmicbox/cosmicbox.conf file, then copied into /cosmicbox/etc directory.
If all key elements are gathered (Wi-Fi key, storage device), then LibraryBox
launches.

## CONFIGURATION OF THE COSMICBOX

By default, the CosmicBox project is configured to only recognize WiFi
USB antennaes Wi-Pi. However, it is possible to configure buildroot to
add new antennae.

To do so, go into project root directory, and enter:

    make linux-menuconfig

In Device Drivers > Network device support > Wireless LAN, select your antenna
driver.

Some drivers need particular firmware, that is a small piece of code that is
uploaded directly to the device for it to function correctly. If so, enter:

    make menuconfig

In Target packages > Hardware handling > Firmware > WiFi firmware, select
the firmware required by your driver (WiFi firmware is visible when Linux
firmware is checked).

To configure your LibraryBox, edit board/cosmicbox/cosmicboxfs/cosmicbox.conf.

## BUILD YOUR COSMICBOX

In project root directory, enter:

    make

You should serv yourself a coffee since this operation may take some time ;-)

Once build is over, you get this file tree:

    buildroot-2015.02/output/images
    +-- cosmicboxfs.tar.gz
    +-- rpi-firmware/
    |   +-- bootcode.bin
    |   +-- cmdline.txt
    |   +-- config.txt
    |   +-- fixup.dat
    |   `-- start.elf
    `-- zImage

## PREPARE THE SD CARD

More information, visit
http://elinux.org/RPi_Advanced_Setup#Advanced_SD_card_setup

You have to create two partitions on the SD card:
 - the first one in fat32 and marked bootable;
 - the second one in ext2.

NB: Nowadays, SD cards have particularly large storage capacities. It is not
    necessary to use the whole available space.
    For the first partition, 50MiB is largely enough. Same goes for the second one.

## INSTALL THE SYSTEM ON THE CARD

To mount partitions (adjust 'sdX' to match your SD card device):

    sudo mount /dev/sdX1 /mnt/mountpointboot
    sudo mount /dev/sdX2 /mnt/mountpointlibrarybox

At the root of the first partition, the RaspberryPi must find the following
files:

    * bootcode.bin
    * cmdline.txt
    * config.txt
    * fixup.dat
    * start.elf
    * zImage

At the root of the second partition, untar the archive containing LibraryBox
file system:

    tar xzf cosmicboxfs.tar.gz -C /mnt/mountpointlibrarybox

Umount all the partitions:

    sudo umount /mnt/mountpointboot
    sudo umount /mnt/mountpointlibrarybox

And eject your SD card.

## INSTALL THE COSMICBOX TO THE STORAGE DEVICE

You have to create one partition in fat32 on the storage device.

To mount this partition (adjust 'sdX" to match your storage device):

    sudo mount /dev/sdX1 /mnt/mountpoint

Copy the content of librarybox directory to /mnt/mountpoint.

Unmount the partition:

    sudo umount /mnt/mountpoint

And eject your storage device.

## START THE COSMICBOX

Insert the SD card into your RaspberryPi. Meanwhile, plug in both the
Wi-Fi antenna and the storage device that contains the second part of
Library website. Power up your RaspberryPi. The new system should come up.
