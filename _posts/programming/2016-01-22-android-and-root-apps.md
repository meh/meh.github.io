---
title: Android and root apps
categories: [programming, android]
layout: post
---

Recently I've been playing WATCH\_DOGS and I thought, I want to be a cool
hacker too!

Now, I don't have a phone, but I have a shitty Galaxy Player 4" running CM11,
so I could still write something for it.

Whence started my journey through the ocean of shit and piss that is Android
development.

As it turns out, one does not simply make an app run as root, because, well,
it's just not really how it works.

Some people say to just execute `su` and it will magically make the app root,
but that's really not the case, the SuperSU interface pops up and ask you if
you want to give the privileges, but then `su` will just run a root shell and
do nothing.

So how do you actually get to run some code as root? With a lot of pain and
blood coming out of every one of your orifices.

Building an executable
----------------------
First of all we need to build an executable, which seems an apparently easy
task, but actually requires quite a bit of fiddling and research to make it
work, or just reading this.

Assuming we have our Android SDK and NDK installed and set up properly, create
a `jni/` directory and get ready.

First our nice `jni/Application.mk`, which contains some general NDK
environment machinery.

```make
# This is the GCC version, because we want the latest one, don't we?
NDK_TOOLCHAIN_VERSION := 4.9

# This is the minimum Android version, and CM11 is on 19.
APP_PLATFORM := android-19

# This is only if we're going to use C++, otherwise shit will break.
APP_STL := gnustl_static
```

And then the actual Makefile, `jni/Android.mk`.

```make
# Whatever, internal magic.
LOCAL_PATH := $(call my-dir)
include $(CLEAR_VARS)

# The name of the executable.
LOCAL_MODULE := executable

# Flags for C++14, also colors, everyone likes colors.
LOCAL_CPPFLAGS := -std=gnu++1y -fexceptions -frtti -fdiagnostics-color=always

# Flags for C, and again, colors.
LOCAL_CFLAGS := -std=gnu11 -fdiagnostics-color=always

# If you want Android logging support.
LOCAL_LDLIBS := -llog

# List of source files to build.
LOCAL_SRC_FILES := main.cpp

# List of directories to include, this one in case you want to separate headers
# and source files.
LOCAL_C_INCLUDES += jni/include

# And here the actual magic trick that makes the build system build an
# executable, also dragons.
include $(BUILD_EXECUTABLE)
```

Then run `ndk-build`, and ta-dah, an executable in `libs/armeabi`.

Using external libraries
------------------------
Using external libraries is most of the time a huge pain in the ass, personally
I just add them as submodules inside `jni/vendor` and then add the proper
definitions to `Android.mk`.

As an easy alternative, there are a bunch of libraries ready for the NDK in
[this][1] nice GitHub organization.

I needed to use `libpcap`, so I just added the submodule, checked out the
`kitkat-release` branch, and added the following to `Android.mk`.

```make
# The name of the produced static library.
LOCAL_STATIC_LIBRARIES += libpcap

# The external library include MUST be AFTER the magic trick.
include $(BUILD_EXECUTABLE)

# Or whatever the path is.
include jni/vendor/pcap/Android.mk
```

Always make sure to checkout the proper branch in the submodule, because it
might not compile otherwise.

Embedding the executable in an app
----------------------------------
Now that we have our executable we need a way to add it to the app and have it
installed on the system so it can be ran.

Luckily Android provides a nice resource directory for this kind of thing,
`res/raw/`, and we're going to abuse assets to our advantage.

Assuming we have copied the built executable to `res/raw/executable`, we're
going to extend the `Application` class to do the installation step.

```java
import java.io.File;
import java.io.InputStream;
import java.io.FileOutputStream;

import android.util.Log;
import android.content.Context;

// I'm going to use Guava for copying an InputStream to an OutputStream,
// you can use anything.
import com.google.common.io.ByteStreams;

public class Application extends android.app.Application() {
  @Override
  public void onCreate() {
    super.onCreate();

    Context context = getApplicationContext();

    try {
      // Get the already installed executable.
      File installed = getFileStreamPath("executable");

      // Fetch the last update time of the app.
      long updated = context.getPackageManager()
        .getPackageInfo(context.getPcakageName(), 0)
        .lastUpdatedTime;

      // Check if the executable has been already installed or if the
      // binary is older than the last update.
      if (!installed.exists() || installed.lastModified() < updated) {
        // Get the stream to our embedded executable.
        InputStream input = context.getResources()
          .openRawResource(R.raw.executable);

        // Create the output stream to the app local directory.
        FileOutputStream output = context
          .openFileOutput("executable", Context.MODE_PRIVATE);

        // Copy the contents.
        ByteStreams.copy(input, output);

        // Remember to close the streams.
        input.close();
        output.close();

        // Make the file actually executable.
        getFileStreamPath("executable").setExecutable(true);
      }
    }
    catch (Exception e) {
      Log.e("welp", "installation failed");
    }
  }
}
```

And that's it, when the app is ran it will check if the executable needs
updating and if it does it will get installed.

One-shot executable
-------------------
If the executable has to just be ran and it does its thing, you just have to
define the command line and then call it.

```java
// Define the command string to call su, the third element is the actual
// command that will get executed, so make sure it's properly escaped.
String[] cmd = { "su", "-c", getFileStreamPath("executable").getPath() };

// Run the command.
Runtime.getRuntime().exec(cmd).waitFor();
```

Long-running service-like executable
------------------------------------
In my case, I needed the executable to keep running and do various things:
taking requests, sending responses, and sending events.

This complicates things quite a bit because it means we have to do IPC.

I resolved this by having a foreground `Service` that runs the executable as
root and then communicates with it using `stdin`/`stdout`.

As serialization protocol I picked [msgpack][2] because it's small, fast and
most importantly the C++ library is just a single-header include.

Building it up
--------------
Since running commands and copying shit around is boring, we can just extend
`build.gradle` to do it for us.

```groovy
task packageExecutable(type: Copy) {
  from 'libs/armeabi/executable'
  into 'src/main/res/raw/'
}

task ndkBuild(type: Exec, description: "Task to run ndk-build") {
  commandLine 'ndk-build'
}

task ndkClean(type: Exec, description: "Task to run ndk-build clean"){
  commandLine 'ndk-build', 'clean'

  doLast {
    delete 'src/main/res/raw/'
  }
}

packageExecutable.dependsOn 'ndkBuild'
preBuild.dependsOn 'packageExecutable'
clean.dependsOn 'ndkClean'
```

Good luck with your Android bullshit.

[1]: https://github.com/android/?query=platform_external
[2]: http://msgpack.org/index.html
