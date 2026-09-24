<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/header.php'); ?>


    <!-- ==== User List Page ==== -->
    <div class="main-panel">
        <div class="content-wrapper">

        <div class="container mt-5">
        <h1 class="text-center mb-4">User List</h1>

        <!-- Success or Error Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success'); ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>

        <!-- Search Form -->
        <form action="<?= base_url('/user-list') ?>" method="get" class="mb-3 User_search">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search users by name, email, or phone" value="<?= esc($search) ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        <!-- User Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Phone Number</th>
                        <th>Email Address</th>
                        <th>Registered At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= esc($user['id']) ?></td>
                                <td><?= esc($user['username']) ?></td>
                                <td><?= esc($user['phone']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td><?= esc($user['registered_at']) ?></td>
                                <td>
                                    <a href="<?= base_url('/user-delete/' . $user['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

       <!-- Pagination -->
       <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Previous Button -->
                <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('/user-list?page=' . ($currentPage - 1) . '&search=' . $search) ?>" tabindex="-1">Previous</a>
                </li>

                <!-- Page Numbers -->
                <?php
                $totalPages = ceil($totalRecords / $perPage);
                for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>">
                        <a class="page-link" href="<?= base_url('/user-list?page=' . $i . '&search=' . $search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Next Button -->
                <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= base_url('/user-list?page=' . ($currentPage + 1) . '&search=' . $search) ?>">Next</a>
                </li>
            </ul>
        </nav>



    </div>

        </div>


<!-- ===== Footer Section ===== -->
<?php include(APPPATH . 'Views/footer.php'); ?>

<style>
    .pagination {
        justify-content: center;
    }
    
    .User_search{
        width: 45%;
        background: #ffffff;
    }
</style>
