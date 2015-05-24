State Pattern
=============

Context Workflow
----------------

In order to ensure all the possible transitions are tested,
instead of implementing all the possible conditions in the context object,
the transitions are represented in each state implementations.

+----------+-----------+-------------+
| is legal | original  | destination |
+----------+-----------+-------------+
| false    | enable    | enable      |
| true     | suspended | enable      |
| true     | disabled  | enable      |
| true     | enable    | suspended   |
| false    | suspended | suspended   |
| false    | disabled  | suspended   |
| true     | enable    | disabled    |
| false    | suspended | disabled    |
| false    | disabled  | disabled    |
+----------+-----------+-------------+
