#!/usr/bin/perl -w

while ( <STDIN> ) {
    my $line = $_;
    chomp ( $line ) ;
    for ( $line ) {
        s/-->.*<--/\e[1;44m$&\e[0m/gi; #tail multiples files name in blue background
        s/.*\[fatal.*|at .*/\e[0;31m$&\e[0m/gi;  #java errors & stacktraces in red
        s/\[error.*/\e[1;31m$&\e[0m/gi;  #errors in light red
        s/payapi-.*/\e[0;32m$&\e[0m/gi; #info replacement in green
        s/___.*/\e[1;33m$&\e[0m/gi; #total execution replacement in yellow
        s/\[warning.*/\e[0;33m$&\e[0m/gi; #warning replacement in yellow
    }
    print $line , "\n" ;
}
