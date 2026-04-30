<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MineShot 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./main.css">
</head>
<body>
    <!-- Header -->
    <nav class="flex flex-col">
        <div class="menu flex jc-spbt ai-cntr">
            <div class="flex jc-spbt ai-cntr">
                <!-- Hamburger Menu Svg -->
                <svg class="hamburger-menu" viewBox="0 0 24 24">
                    <path d="M 2 5 L 2 7 L 22 7 L 22 5 L 2 5 z M 2 11 L 2 13 L 22 13 L 22 11 L 2 11 z M 2 17 L 2 19 L 22 19 L 22 17 L 2 17 z"/>
                </svg>
    
                <!-- Adani logo -->
                <img src="./assets/growth-adani-logo.png" alt="adani logo" class="logo m-l-10">
            </div>
    
            <div>
                <p class="company">PEKB</p>
            </div>
            <div class="flex jc-spbt ai-cntr">
                <p class="profile-name m-r-10">Arjun Jain</p>
                <div>
                    <img src="./assets/Avatar.png" alt="Profile image">
                </div>
            </div>
        </div>
        <div class="header-image">
            <img src="./assets/Header-Image.png" alt="Header image">
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Add Drill View -->
        <div id="AddDrill" class="hide">
        <!-- Add Drilling Data Screen -->
            <div class="body-menu flex jc-spbt ai-cntr">
                <div>
                    <p class="body-heading">Add Drilling Data</p>
                </div>
                <div class="flex jc-spbt ai-cntr">
                    <div class="input-outline flex jc-spbt ai-cntr m-r-10 w-150px">
                        <input type="date">
                    </div>
                    <select class="form-select w-150px" aria-label="select example">
                        <option selected="true" disabled>Select Shift</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </select>
                </div>
                <div>
                    <button type="button" class="btn btn-primary input-save" data-bs-toggle="modal" data-bs-target="#exampleModal">SAVE</button>
                </div>
            </div>

            <div class="body-container">
                <div class="body-outline">
                    <table>
                        <tr>
                            <th>Drilling material</th>
                            <th>Drill Machine No.</th>
                            <th>Working Area</th>
                            <th>No of Holes Drilled</th>
                            <th>Burden(m)</th>
                            <th>Hole Depth(m)</th>
                            <th>Spacing(m)</th>
                            <th>Type of Drilling</th>
                            <th>Bench Height(m)</th>
                            <th>Tools</th>
                        </tr>
                        <tr>
                            <td>Coal</td>
                            <td>CG4DM5376</td>
                            <td>Salhi Pit</td>
                            <td>889</td>
                            <td>8.00</td>
                            <td>8.00</td>
                            <td>8.00</td>
                            <td>Normal</td>
                            <td>8.00</td>
                            <td>
                                <svg viewBox="0 0 18 19" class="action-icon">
                                    <path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z" fill="#2F80ED"/>
                                    <path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z" fill="#2F80ED"/>
                                </svg>
                            </td>
                        </tr>
                    </table>
                    <div class="add-btn flex jc-cntr ai-cntr">
                        <svg width="16" height="16" viewBox="0 0 16 16">
                            <path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z"/>
                        </svg>                        
                    </div>
                </div>
            </div>
        </div>

        <div id="ListDrill" class="show">
            <div class="body-menu d-flex">
                <div>
                    <p class="body-heading">List Drilling Data</p>
                </div>
                <div class="d-flex transform-center">
                    <div class="input-outline flex jc-spbt ai-cntr m-r-10 w-150px">
                        <input type="date">
                    </div>
                    <select class="form-select w-150px" aria-label="select example">
                        <option selected="true" disabled>Select Shift</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </select>
                </div>
            </div>
            <div class="body-container flex flex-col justify-content-between align-items-center">
                <div class="body-outline general-list">
                    <table>
                        <tr class="list-general">
                            <th>Date</th>
                            <th>Shift</th>
                            <th>Action</th>
                            <th>Status</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex jc-cntr">
                                    <span>20-10-2021</span>
                                </div>
                                
                            </td>
                            <td>
                                <div class="flex jc-cntr">
                                    <span>A</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-evenly">
                                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
                                        <path d="M17.02 19.5C17.85 19.5 18.5228 18.8284 18.5228 18C18.5228 17.1716 17.85 16.5 17.02 16.5C16.1899 16.5 15.5171 17.1716 15.5171 18C15.5171 18.8284 16.1899 19.5 17.02 19.5Z" fill="#3D89CF"/>
                                        <path d="M22.8639 17.6093C22.3996 16.4287 21.5984 15.41 20.5596 14.6796C19.5208 13.9491 18.2901 13.539 17.02 13.5C15.7499 13.539 14.5192 13.9491 13.4804 14.6796C12.4416 15.41 11.6404 16.4287 11.1761 17.6093L11.0085 18L11.1761 18.3907C11.6404 19.5713 12.4416 20.59 13.4804 21.3204C14.5192 22.0509 15.7499 22.461 17.02 22.5C18.2901 22.461 19.5208 22.0509 20.5596 21.3204C21.5984 20.59 22.3996 19.5713 22.8639 18.3907L23.0314 18L22.8639 17.6093ZM17.02 21C16.4255 21 15.8444 20.8241 15.3501 20.4944C14.8558 20.1648 14.4706 19.6962 14.2431 19.148C14.0156 18.5999 13.956 17.9967 14.072 17.4147C14.188 16.8328 14.4743 16.2982 14.8946 15.8787C15.315 15.4591 15.8506 15.1734 16.4336 15.0576C17.0167 14.9419 17.621 15.0013 18.1702 15.2284C18.7195 15.4554 19.1889 15.8399 19.5192 16.3333C19.8494 16.8266 20.0257 17.4067 20.0257 18C20.0247 18.7953 19.7077 19.5578 19.1443 20.1202C18.5808 20.6826 17.8169 20.999 17.02 21Z" fill="#3D89CF"/>
                                        <path d="M5.74854 12.75H9.50569V14.25H5.74854V12.75Z" fill="#3D89CF"/>
                                        <path d="M5.74854 9H14.7657V10.5H5.74854V9Z" fill="#3D89CF"/>
                                        <path d="M5.74854 5.25H14.7657V6.75H5.74854V5.25Z" fill="#3D89CF"/>
                                        <path d="M17.0201 1.5H3.49432C3.0961 1.50119 2.71453 1.6596 2.43295 1.94065C2.15136 2.2217 1.99264 2.60254 1.99146 3V21C1.99264 21.3975 2.15136 21.7783 2.43295 22.0593C2.71453 22.3404 3.0961 22.4988 3.49432 22.5H9.50577V21H3.49432V3H17.0201V11.25H18.5229V3C18.5218 2.60254 18.363 2.2217 18.0815 1.94065C17.7999 1.6596 17.4183 1.50119 17.0201 1.5Z" fill="#3D89CF"/>
                                    </svg>
                                    
                                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
                                        <path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#3D89CF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#3D89CF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                        
                                    <svg viewBox="0 0 18 19" class="action-icon">
                                        <path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z" fill="#2F80ED"/>
                                        <path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z" fill="#2F80ED"/>
                                    </svg>
                                </div>

                            </td>
                            <td>
                                <div class="flex jc-cntr">
                                    <div class="circle bg-success"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex jc-cntr">
                                    <span>20-10-2021</span>
                                </div>
                                
                            </td>
                            <td>
                                <div class="flex jc-cntr">
                                    <span>A</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-evenly">
                                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
                                        <path d="M17.02 19.5C17.85 19.5 18.5228 18.8284 18.5228 18C18.5228 17.1716 17.85 16.5 17.02 16.5C16.1899 16.5 15.5171 17.1716 15.5171 18C15.5171 18.8284 16.1899 19.5 17.02 19.5Z" fill="#3D89CF"/>
                                        <path d="M22.8639 17.6093C22.3996 16.4287 21.5984 15.41 20.5596 14.6796C19.5208 13.9491 18.2901 13.539 17.02 13.5C15.7499 13.539 14.5192 13.9491 13.4804 14.6796C12.4416 15.41 11.6404 16.4287 11.1761 17.6093L11.0085 18L11.1761 18.3907C11.6404 19.5713 12.4416 20.59 13.4804 21.3204C14.5192 22.0509 15.7499 22.461 17.02 22.5C18.2901 22.461 19.5208 22.0509 20.5596 21.3204C21.5984 20.59 22.3996 19.5713 22.8639 18.3907L23.0314 18L22.8639 17.6093ZM17.02 21C16.4255 21 15.8444 20.8241 15.3501 20.4944C14.8558 20.1648 14.4706 19.6962 14.2431 19.148C14.0156 18.5999 13.956 17.9967 14.072 17.4147C14.188 16.8328 14.4743 16.2982 14.8946 15.8787C15.315 15.4591 15.8506 15.1734 16.4336 15.0576C17.0167 14.9419 17.621 15.0013 18.1702 15.2284C18.7195 15.4554 19.1889 15.8399 19.5192 16.3333C19.8494 16.8266 20.0257 17.4067 20.0257 18C20.0247 18.7953 19.7077 19.5578 19.1443 20.1202C18.5808 20.6826 17.8169 20.999 17.02 21Z" fill="#3D89CF"/>
                                        <path d="M5.74854 12.75H9.50569V14.25H5.74854V12.75Z" fill="#3D89CF"/>
                                        <path d="M5.74854 9H14.7657V10.5H5.74854V9Z" fill="#3D89CF"/>
                                        <path d="M5.74854 5.25H14.7657V6.75H5.74854V5.25Z" fill="#3D89CF"/>
                                        <path d="M17.0201 1.5H3.49432C3.0961 1.50119 2.71453 1.6596 2.43295 1.94065C2.15136 2.2217 1.99264 2.60254 1.99146 3V21C1.99264 21.3975 2.15136 21.7783 2.43295 22.0593C2.71453 22.3404 3.0961 22.4988 3.49432 22.5H9.50577V21H3.49432V3H17.0201V11.25H18.5229V3C18.5218 2.60254 18.363 2.2217 18.0815 1.94065C17.7999 1.6596 17.4183 1.50119 17.0201 1.5Z" fill="#3D89CF"/>
                                    </svg>
                                    
                                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
                                        <path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#3D89CF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#3D89CF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                        
                                    <svg viewBox="0 0 18 19" class="action-icon">
                                        <path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z" fill="#2F80ED"/>
                                        <path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z" fill="#2F80ED"/>
                                    </svg>
                                </div>

                            </td>
                            <td>
                                <div class="flex jc-cntr">
                                    <div class="circle"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="flex jc-cntr">
                                    <span>20-10-2021</span>
                                </div>
                                
                            </td>
                            <td>
                                <div class="flex jc-cntr">
                                    <span>A</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-evenly">
                                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
                                        <path d="M17.02 19.5C17.85 19.5 18.5228 18.8284 18.5228 18C18.5228 17.1716 17.85 16.5 17.02 16.5C16.1899 16.5 15.5171 17.1716 15.5171 18C15.5171 18.8284 16.1899 19.5 17.02 19.5Z" fill="#3D89CF"/>
                                        <path d="M22.8639 17.6093C22.3996 16.4287 21.5984 15.41 20.5596 14.6796C19.5208 13.9491 18.2901 13.539 17.02 13.5C15.7499 13.539 14.5192 13.9491 13.4804 14.6796C12.4416 15.41 11.6404 16.4287 11.1761 17.6093L11.0085 18L11.1761 18.3907C11.6404 19.5713 12.4416 20.59 13.4804 21.3204C14.5192 22.0509 15.7499 22.461 17.02 22.5C18.2901 22.461 19.5208 22.0509 20.5596 21.3204C21.5984 20.59 22.3996 19.5713 22.8639 18.3907L23.0314 18L22.8639 17.6093ZM17.02 21C16.4255 21 15.8444 20.8241 15.3501 20.4944C14.8558 20.1648 14.4706 19.6962 14.2431 19.148C14.0156 18.5999 13.956 17.9967 14.072 17.4147C14.188 16.8328 14.4743 16.2982 14.8946 15.8787C15.315 15.4591 15.8506 15.1734 16.4336 15.0576C17.0167 14.9419 17.621 15.0013 18.1702 15.2284C18.7195 15.4554 19.1889 15.8399 19.5192 16.3333C19.8494 16.8266 20.0257 17.4067 20.0257 18C20.0247 18.7953 19.7077 19.5578 19.1443 20.1202C18.5808 20.6826 17.8169 20.999 17.02 21Z" fill="#3D89CF"/>
                                        <path d="M5.74854 12.75H9.50569V14.25H5.74854V12.75Z" fill="#3D89CF"/>
                                        <path d="M5.74854 9H14.7657V10.5H5.74854V9Z" fill="#3D89CF"/>
                                        <path d="M5.74854 5.25H14.7657V6.75H5.74854V5.25Z" fill="#3D89CF"/>
                                        <path d="M17.0201 1.5H3.49432C3.0961 1.50119 2.71453 1.6596 2.43295 1.94065C2.15136 2.2217 1.99264 2.60254 1.99146 3V21C1.99264 21.3975 2.15136 21.7783 2.43295 22.0593C2.71453 22.3404 3.0961 22.4988 3.49432 22.5H9.50577V21H3.49432V3H17.0201V11.25H18.5229V3C18.5218 2.60254 18.363 2.2217 18.0815 1.94065C17.7999 1.6596 17.4183 1.50119 17.0201 1.5Z" fill="#3D89CF"/>
                                    </svg>
                                    
                                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="action-icon">
                                        <path d="M17.1102 5.40807L19.2322 7.52507L17.1102 5.40807ZM18.4748 3.54307L12.7368 9.27007C12.4404 9.56557 12.2382 9.94205 12.1557 10.3521L11.6257 13.0001L14.2788 12.4701C14.6896 12.3881 15.0663 12.1871 15.3628 11.8911L21.1008 6.16407C21.2732 5.99197 21.41 5.78766 21.5033 5.56281C21.5966 5.33795 21.6446 5.09695 21.6446 4.85357C21.6446 4.61019 21.5966 4.36919 21.5033 4.14433C21.41 3.91948 21.2732 3.71517 21.1008 3.54307C20.9284 3.37097 20.7237 3.23446 20.4984 3.14132C20.2731 3.04818 20.0316 3.00024 19.7878 3.00024C19.5439 3.00024 19.3025 3.04818 19.0772 3.14132C18.8519 3.23446 18.6472 3.37097 18.4748 3.54307V3.54307Z" stroke="#3D89CF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M19.6409 15V18C19.6409 18.5304 19.4298 19.0391 19.054 19.4142C18.6782 19.7893 18.1686 20 17.6371 20H6.61612C6.08468 20 5.575 19.7893 5.19921 19.4142C4.82342 19.0391 4.6123 18.5304 4.6123 18V7C4.6123 6.46957 4.82342 5.96086 5.19921 5.58579C5.575 5.21071 6.08468 5 6.61612 5H9.62185" stroke="#3D89CF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                        
                                    <svg viewBox="0 0 18 19" class="action-icon">
                                        <path d="M5.14414 15.2656H12.8539L13.2793 6.26562H4.71875L5.14414 15.2656Z" fill="#2F80ED"/>
                                        <path d="M15.1875 5H12.9375V3.59375C12.9375 2.97324 12.433 2.46875 11.8125 2.46875H6.1875C5.56699 2.46875 5.0625 2.97324 5.0625 3.59375V5H2.8125C2.50137 5 2.25 5.25137 2.25 5.5625V6.125C2.25 6.20234 2.31328 6.26562 2.39062 6.26562H3.45234L3.88652 15.459C3.91465 16.0584 4.41035 16.5312 5.00977 16.5312H12.9902C13.5914 16.5312 14.0854 16.0602 14.1135 15.459L14.5477 6.26562H15.6094C15.6867 6.26562 15.75 6.20234 15.75 6.125V5.5625C15.75 5.25137 15.4986 5 15.1875 5ZM6.32812 3.73438H11.6719V5H6.32812V3.73438ZM12.8549 15.2656H5.14512L4.71973 6.26562H13.2803L12.8549 15.2656Z" fill="#2F80ED"/>
                                    </svg>
                                </div>

                            </td>
                            <td>
                                <div class="flex jc-cntr">
                                    <div class="circle bg-danger"></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="add-btn flex jc-cntr ai-cntr">
                        <svg width="16" height="16" viewBox="0 0 16 16">
                            <path d="M6 16H10V10H16V6H10V0H6V6H0V10H6V16Z"/>
                        </svg>                        
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center page-navigation">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                      <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                      <li class="page-item"><a class="page-link" href="#">1</a></li>
                      <li class="page-item"><a class="page-link" href="#">2</a></li>
                      <li class="page-item"><a class="page-link" href="#">3</a></li>
                      <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="flex jc-spbt ai-cntr">
        <p class="software-version">MineShot 2.0</p>
        <p>Designed and Developed by 
            <a href="https://www.tetrain.com/" class="anchor">Tetra Information Services Pvt. Ltd</a>
        </p>
    </footer>

    <!-- All Modals -->
    <!-- 1. PopUpConfirmation Add Drill-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <!-- Todo: Onclick of Submit Show this alert -->
        <div class="alert alert-success alert-dismissible fade hide" role="alert">
            Data Saved Successfully
        </div>
        <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content">
            <div class="modal-header jc-cntr">
              <h5 class="modal-title text-center body-heading" id="exampleModalLabel">Add Drill Final Submit</h5>
              <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <th>Drilling material</th>
                        <th>Drill Machine No.</th>
                        <th>Working Area</th>
                        <th>No of Holes Drilled</th>
                        <th>Burden(m)</th>
                        <th>Hole Depth(m)</th>
                        <th>Spacing(m)</th>
                        <th>Type of Drilling</th>
                        <th>Bench Height(m)</th>
                    </tr>
                    <tr>
                        <td>Coal</td>
                        <td>CG4DM5376</td>
                        <td>Salhi Pit</td>
                        <td>889</td>
                        <td>8.00</td>
                        <td>8.00</td>
                        <td>8.00</td>
                        <td>Normal</td>
                        <td>8.00</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer jc-cntr">
                <button type="button" class="btn btn-primary input-save me-5 close" data-dismiss="alert">submit</button>
                <button type="button" class="btn btn-danger input-save" data-bs-dismiss="modal">Discard</button>
            </div>
          </div>
        </div>
    </div>      
    <script src="./index.js"></script>
</body>
</html>
