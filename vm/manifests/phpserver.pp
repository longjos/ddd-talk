Package <| |> ~> Service <| |>
class {'apt': }
exec {'apt-update' : 
	command => '/usr/bin/apt-get update',
} ->
apt::ppa { 'ppa:ondrej/php5': } ->
package {'apache2':
        ensure => present,
    } ->
package {'php5':
        ensure => present,
    } ->
package {'mysql-server':
        ensure => present,
    } ->
package {'mysql-client':
        ensure => present,
    }

service {'apache2':
        ensure => running,
	require => Package['apache2']
    }
service {'mysql':
        ensure => running,
    }

package {'git':
    ensure => present
}




