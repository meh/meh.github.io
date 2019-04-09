---
title: Lies, damned lies and documentation.
categories: [programming, c, embedded, rant, littlefs]
layout: post
comments: 4
---

At work I've been working on a simple encrypted flash abstraction using AES-XTS
on the block layer and
[littlefs](https://os.mbed.com/blog/entry/littlefs-high-integrity-embedded-fs/)
as file system, which reading the blog post has some really nifty features when
dealing with microcontrollers.

Kind of sadly the project is currently under a bunch of almost finalized huge
changes which would end up in a v2 release, these changes are actually needed
because of custom file attributes, and the data format is incompatible between
v1 and v2 so the choice must be made now.

While working on some debugging functionality, for example `tree` like output
to serial, I had to use the `lfs_dir_*` functions. The API is straightforward.

```c
// Open a directory
//
// Once open a directory can be used with read to iterate over files.
// Returns a negative error code on failure.
int lfs_dir_open(lfs_t *lfs, lfs_dir_t *dir, const char *path);

// Close a directory
//
// Releases any allocated resources.
// Returns a negative error code on failure.
int lfs_dir_close(lfs_t *lfs, lfs_dir_t *dir);

// Read an entry in the directory
//
// Fills out the info structure, based on the specified file or directory.
// Returns a negative error code on failure.
int lfs_dir_read(lfs_t *lfs, lfs_dir_t *dir, struct lfs_info *info);
```

As is typical in C, errors are returned as negative numbers and success is
returned as `0`.

In some cases when the function should return a value as well _(for example
reading from a file descriptor and wanting to know how many bytes were read)_
the number of bytes can be returned, but common sense here states that it's not
going to return any bytes since it's just advancing to the next directory.

As nothing is clearly specified the other assumption is that when there are no
more directories an error would be returned, and without further reading you
just do a `while (lfs_dir_read(...) == 0)` and call it a day.

Except that prints nothing. At all. At which point you start blaming the world
for the life decisions that brought you to this point and you go read the
fucking source code for `lfs_dir_read`, and here it is in
[full](https://github.com/ARMmbed/littlefs/blob/master/LICENSE.md).

```c
int lfs_dir_read(lfs_t *lfs, lfs_dir_t *dir, struct lfs_info *info) {
    memset(info, 0, sizeof(*info));

    // special offset for '.' and '..'
    if (dir->pos == 0) {
        info->type = LFS_TYPE_DIR;
        strcpy(info->name, ".");
        dir->pos += 1;
        return 1;
    } else if (dir->pos == 1) {
        info->type = LFS_TYPE_DIR;
        strcpy(info->name, "..");
        dir->pos += 1;
        return 1;
    }

    while (true) {
        if (dir->id == dir->m.count) {
            if (!dir->m.split) {
                return false;
            }

            int err = lfs_dir_fetch(lfs, &dir->m, dir->m.tail);
            if (err) {
                return err;
            }

            dir->id = 0;
        }

        int err = lfs_dir_getinfo(lfs, &dir->m, dir->id, info);
        if (err && err != LFS_ERR_NOENT) {
            return err;
        }

        dir->id += 1;
        if (err != LFS_ERR_NOENT) {
            break;
        }
    }

    dir->pos += 1;
    return true;
}
```

As it turns out the value returned on success is actually `1` _(and `true`
sometimes, because why not)_ and `false` when there are no more directories.
Which is reasonable since it's essentially an iterator. Except the
documentation assumed the reader would assume something that isn't
_"standard"_. (Now I just hope that this behavior is _"standard"_ within the
rest of the library, so that my assumptions don't turn out to be wrong again.).

If there was no documentation in the first place, I would have gone and read
the code directly insted of relying on the documentation, instead I believed
the documentation.

So apparently there are three kind of lies: lies, damned lies and
documentation. Please make sure your documentation is up to date and correct,
or remove it entirely. No documentation is miles better than inaccurate
documentation.
