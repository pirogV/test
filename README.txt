���������
����������� ����� �� ������.
���������� ��������� �� http:// - /www
���� ���� � ������� SQL
��������� ����������� � ���� � ����� /processes/common/Db.php
	private 		$host 		= '127.0.0.1';
	private 		$dbname 	= 'discount';
	private 		$user 		= 'root';
	private 		$password 	= '';
�� *nix �������� �� ����������

�����������
�����������, MVC
���� ����� - /www/index.php
�������������, ��� - /processes/Start.php
����������, ������, ���� - ���������� /processes/controller|models|views
��������������� ����� (�������, ���� ������, ���������, ������� ���� �����������, �����) - /processes/common/
��������� - /processes/templates/
������ - /processes/modules/
������������ - /processes/config/

��� �������� PHP
�������� ��� ����������� �����������.
������ ���� �����, ��� ������������ ������, �������� ������(PSR-4), ���� �������������.
� ����� ������������� ��������� REQUEST_URI �� ��������� (���), ������������� ����������� �� ��� ��� ������������ (���� �� a-z\/\- NotFound)
���������� ���������, ��������� ������ MVC ����� � ����� ������ (��������� ���������� ��������� ������ ����� �����������, �������� ��������� � ������� ���������)
������ ���������� ��������, ������� ����� ������ ��� ������, ����������� ��������� ������ MVC � �������� �� ������ � ����� �������.

Ajax
������� ������ � Jquery
�� ������ ������ ���������� ������� ($(document).on('click', '.ajax', func........))
C ���� ������ �������� ������� �������� ����������� ajax-�������, ������������� �������� �� ���������, �������� ������ � ����������� ������� ���������� ������
��������, ������ <a class="ajax" box="content" form="myform" effect="insert"....
�������� ������ ��������� ������ � ����� myform, ������� ����� � ��������� � id="content" �������� ������ ������� insert
�� ������� ������� ���� ���������� ���������� ajax-������ � ������ ����� ��������� MVC (��� ���������)
������ ������ ��������� 'HTTP_X_REQUESTED_WITH' ��������� ������ �� ��������� �� �������, � ���������������� ������ ������� �� ������, 
����� ����-�� ���� ajax ��������� (���� url?ajax=yes), �� ������ ��� ������.

�� �������
���� ����, ��������� ������ �������� �� ������ ������ ����������, ��� �������. ����� ������ ��� ���� (���������� ������� ���), ������� ���������, name ���������.
���������� ����� ���������� ����� ��� �����-�� ��������� �� ���������� ��������� ����� ������� �� ����� ����������. ������ ����� ������ ����� ����� �� �����.
���� ��� ������ � ����� ������ ������� �� PDO, �� ������������ ������������, �� ���������� �������������� �������, ����� ��������� SQL ����������� SET, IN
����� ������ ��� ������������ ������.
���������� ������ ���������� ����������� ���������� ��� �������.

��� ���������
������� ����������� ��� ������������ ������. ��������� ��������� ��� ������� � ���� �������, �� ��������� ���� ��� ���������� �������� ���� ����������� �������� � ������������ �� ���.

��� �� �������
�������� (PHPDoc) �� ����� (����� �������� ��������)
������������ � ������ ����������� ����, ������ ������ (�� ������ ���������, ������������ ������� ��� ������� ��������).
��������� ������� ����������� ��� �������� �� ���� ������ ������ ��� ���� �������������.




