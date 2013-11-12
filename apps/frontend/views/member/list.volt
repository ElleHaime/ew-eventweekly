{% extends "layouts/base.volt" %}

{% block content %}
<h1>Profile</h1>
<div align="left">
		<table>
			<tr>
				<td>Email: </td>
				<td>{{ member.email }}</td>
			</tr>

			<tr>
				<td>Name:</td>
				<td>{{ member.name }}</td>
			</tr>

			<tr>
				<td>Address:</td>
				<td>{{ member.address }}</td>
			</tr>

			<tr>
				<td>Phone:</td>
				<td>{{ member.phone }}</td>
			</tr>

			<tr>
				<td>Location:</td>
				<td>
					{{ member.location.name }}
				</td>
			</tr>			

		</table>
	</form>
</div>

{% endblock %}