---
title: Bye bye data
categories: [random]
layout: post
---

A few days ago the hard disk of my laptop started having bad problems, the home
partition remounted itself readonly, and a huge series of I/O errors were
showing in `dmesg`.

I hastily backed up the whole disk using `dd` with `conv=noerror`, I thought
that was a good idea and I was just going to have some holes around and maybe
one or two corrupted files, nothing major.

After the event I contacted Dell to make them send a new hard disk, I used the
laptop normally for the next few days, and after the Dell technician showed up
I backed up again just the home partition.

After following the security protocol with the new hard disk, I started putting
back the data from the fresh backup.

Everything went fine, then I started up X, and Fluxbox was acting weird. I
thought the technician broke the keyboard while fixing another issue and
started raging, the keyboard worked perfectly in tty though, so I thought there
was something else.

I started up looking around the config, and at my dismay the *keys* file was
completely corrupted. I slightly paniced.

"How extended was the corruption? Well, fuck this shit, let's open the old home
backup."

The old backup was completely corrupted, no filesystem was found on it.

"What the fuck is going on? Ok, I'll just wipe the current home and start from
scratch getting the files I need while configuring."

My *mail* folder was corrupted to no end.

"Ok, I lost emails, lucky I've got a backup of it two weeks old."

"Wait, what if the passwords file is corrupted? What the fuck am I gonna do
without all my passwords? Ok, here goes nothing."

And GPG didn't find the private key. I thought about suicide for a moment, then
I hoped I wasn't as dumb as I thought.

At least I backed up the new GPG I had recently created, and the password file
wasn't corrupted. It could have been worse.

Incidentally I lost the whole */data* partition, because the full backup is
completely useless, but I wasn't so sad about it, just few virtual machines
gone to sleep.

What did I learn from this?
---------------------------
If the disk is fucked, `dd` with `conv=noerror` isn't that smart, better use
`ddrescue`.

Using rotating images of the home partition is dumb, better use differential
backups. If I hadn't been paranoid about something breaking in the image
backups I would have shit nothing, no GPG key, no mails, no anything.

I need another 2T drive for backup redundancy.

The bright side
---------------
I now have full disk encryption, with a whole-disk LUKS partition with inside
LVM partitions.

And I cleaned up my projects directory and found out about some configuration
files I should have added to the git repo.
