FROM centos:7

# install php7
ADD src/main/docker/php-fpm/remi.repo /etc/yum.repos.d/remi.repo
ADD src/main/docker/php-fpm/remi.public /etc/pki/rpm-gpg/RPM-GPG-KEY-remi

RUN yum -y install epel-release && yum clean all
RUN yum -y install php70 php70-php-fpm php70-php-intl php70-php-gd php70-php-mbstring php70-php-pdo php70-php-mysqlnd php70-php-pecl-apcu php70-php-xml

ADD src/main/docker/php-fpm/www.conf /etc/opt/remi/php70/php-fpm.d/www.conf
ADD src/main/docker/php-fpm/project.ini /etc/opt/remi/php70/php.d/60-project.ini

RUN useradd nginx

# install nginx
RUN yum -y install nginx
ADD src/main/docker/nginx/tapalava.conf /etc/nginx/conf.d/tapalava.conf

EXPOSE 80
EXPOSE 443

ADD . /var/www/tapalava
RUN chown nginx:nginx -R /var/www/tapalava
RUN ln -s /var/www/tapalava/web /var/www/tapalava-public
CMD  nginx -g 'daemon on;' && /opt/remi/php70/root/usr/sbin/php-fpm -F
