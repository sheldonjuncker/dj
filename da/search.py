from jung import Jung
import sys

if len(sys.argv) > 1:
    j = Jung()
    print(j.search(sys.argv[1]))
    exit(0)
else:
    print("Missing search term.")
    exit(0)
