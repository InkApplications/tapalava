FROM centos:7

# install php7
ADD src/main/docker/php-fpm/remi.repo /etc/yum.repos.d/remi.repo
ADD src/main/docker/php-fpm/remi.public /etc/pki/rpm-gpg/RPM-GPG-KEY-remi

RUN yum -y install epel-release && yum clean all
RUN yum -y install php71 php71-php-fpm php71-php-intl php71-php-gd php71-php-mbstring php71-php-pdo php71-php-mysqlnd php71-php-pecl-apcu php71-php-xml

ADD src/main/docker/php-fpm/www.conf /etc/opt/remi/php71/php-fpm.d/www.conf
ADD src/main/docker/php-fpm/project.ini /etc/opt/remi/php71/php.d/60-project.ini

RUN useradd nginx

# install nginx
RUN yum -y install nginx
ADD src/main/docker/nginx/tapalava.conf /etc/nginx/conf.d/tapalava.conf

EXPOSE 80
EXPOSE 443

ADD . /var/www/tapalava
RUN chown nginx:nginx -R /var/www/tapalava
RUN ln -s /var/www/tapalava/web /var/www/tapalava-public
CMD  nginx -g 'daemon on;' && /opt/remi/php71/root/usr/sbin/php-fpm -F
