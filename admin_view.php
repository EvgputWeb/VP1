<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Burgers Admin</title>
</head>
<body>

    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->

    <table>
        <thead>
            <tr>
                <td colspan="4">Пользователи</td>
            </tr>
            <tr>
                <td>id</td>
                <td>name</td>
                <td>email</td>
                <td>phone</td>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $sth_users->fetch()) : ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['phone']; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->

    <table>
        <thead>
        <tr>
            <td colspan="4">Заказы</td>
        </tr>
        <tr>
            <td>id</td>
            <td>username</td>
            <td>street</td>
            <td>home</td>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $sth_orders->fetch()) : ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['street']; ?></td>
                <td><?php echo $row['home']; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->

</body>
</html>
