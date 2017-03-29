# Migrator

A simple tool used for migration between database table

# Example config

config.yml
```
"Table Alias name":
    source:
        type: pdo_mysql
        connection:
            hostname: db-hostname-from
            database: db-name
            table: db-table
            username: db-user
            password: db-pass
        # The number of rows to retrieve per chunk
        chunk_size: 10000
        # Custom type
        # @todo - design in a manner to have simple usage
        parameters:
            # if the query should prepend an order
            order_query: " ORDER BY `id` DESC"
            # if the queries should have filters
            # at the moment is [field = value]
            filters:
                - country_id: 1
                - another_field 1
            # if we join the table
            join: "INNER JOIN `my_partitioned_table` as `pt` ON `pt`.`source_id` = `db-table`.`id`"
            join_fields:
                # field from `pt` -> as field name
                # Note the alias from the join parameter
                # from these should result a `pt`.`field_1` as `pt_field1`
                pt.field_1:   pt_field1
                pt.field_2:   pt_field2
    destination:
        type: pdo_mysql
        connection:
            hostname: db-hostname-from
            database: db-name
            table: db-table
            username: db-user
            password: db-pass
        parameters:
            # override null values that the new table does not accept 
            # all pt_field1 with null values should have 0 as value in the new table
            override_null_values:
                pt_field1: 0
            # for cases when 1 value is stored different from the other table
            value_mapper:
                # source field name
                # it will replace from the "source-table" status active
                # in "destination-table" status 1
                status:
                    "active":   1
                    "inactive": 0
    mapping:
        # from table column name => to table column name
        id:                 id
        name:               new_table_name
        status:             status
        pt_field1:          my_field
        pt_field2:          my_other_field
```

run example
```
 php bin/bootstrap.php migration:start -c config/config.yml
```