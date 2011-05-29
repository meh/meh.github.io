Versionub, the version parser for nubs
======================================

I've been using Versionomy for quite some time, sadly after tons of fights I found out that
a segfault that was killing packø was versionomy's fault, I've no idea how or why, but it was it.

Waiting for the author to fix it wasn't in my plans, so I implemented something myself, and it is
quite cooler than Versionomy.

Externally it works like Versionomy, so you have `Versionub.parse(text, type)` but writing additional
version schemes is a lot easier.

Like versionomy, example for Windows versioning.

    Versionub.register :windows do
      parser do
        rule(:part) { match['0-9'].repeat }

        rule(:separator) { match['.-_\s'] }

        rule(:version) {
          part.as(:major) >> separator.maybe >>
          str('SP').maybe >> part.as(:minor)
        }

        root :version
      end

      def major
        @data[:major] if @data[:major]
      end

      def minor
        @data[:minor] if @data[:minor]
      end

      include Comparable

      def <=> (value)
        value = Versionub.parse(value)

        if (tmp = (minor <=> value.minor)) != 0
          return tmp
        end

        if (tmp = (major <=> value.major)) != 0
          return tmp
        end

        0
      end
    end

It uses parslet for parsing/transforming and then simply defines methods to use the version object.
