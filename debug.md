# Debug compiled view issue

The issue is that Laravel is compiling the view to d15648ad81eb88f86ce0010b3abe9fb3.php and there's a syntax error at line 65.

This suggests there might be an unclosed @if/@endif or similar issue that's not visible in the source file.

Let me create a simple test view to replace the problematic one temporarily.