FROM ubuntu:14.04

EXPOSE 3306

RUN DEBIAN_FRONTEND=noninteractive \
    apt-get update \
    && apt-get install -y mysql-server

RUN sed -i 's/127.0.0.1/0.0.0.0/g' /etc/mysql/my.cnf

# Here, we should GRANT privileges to allow root connection from other hosts.

CMD /etc/init.d/mysql start && tail -f /var/log/mysql.err /var/log/mysql.log
